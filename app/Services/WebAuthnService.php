<?php

namespace App\Services;

use App\Models\StaffBiometricCredential;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class WebAuthnService
{
    /**
     * Generate Base64URL-encoded cryptographically secure random challenge.
     */
    public function generateChallenge(): string
    {
        return $this->base64UrlEncode(random_bytes(32));
    }

    /**
     * Get Relying Party (RP) configuration.
     */
    public function getRelyingParty(): array
    {
        $host = request()->getHost() ?: (parse_url(config('app.url', 'http://localhost'), PHP_URL_HOST) ?: 'localhost');

        return [
            'name' => config('app.name', 'Showdown Staff Attendance'),
            'id' => $host,
        ];
    }

    /**
     * Generate WebAuthn creation options for registering a biometric/passkey device.
     */
    public function generateRegisterOptions(User $user): array
    {
        $challenge = $this->generateChallenge();
        session(['webauthn_register_challenge' => $challenge]);

        $existingCredentials = $user->biometricCredentials()
            ->where('is_active', true)
            ->get()
            ->map(fn ($cred) => [
                'type' => 'public-key',
                'id' => $cred->credential_id,
                'transports' => $cred->transports ?: ['internal', 'hybrid'],
            ])
            ->toArray();

        return [
            'challenge' => $challenge,
            'rp' => $this->getRelyingParty(),
            'user' => [
                'id' => $this->base64UrlEncode((string) $user->id),
                'name' => $user->email,
                'displayName' => $user->name,
            ],
            'pubKeyCredParams' => [
                ['type' => 'public-key', 'alg' => -7],   // ES256 (ECDSA P-256)
                ['type' => 'public-key', 'alg' => -257], // RS256 (RSA 2048)
            ],
            'authenticatorSelection' => [
                'authenticatorAttachment' => 'platform', // Built-in TouchID, FaceID, Windows Hello
                'userVerification' => 'preferred',
                'residentKey' => 'discouraged',
            ],
            'timeout' => 60000,
            'attestation' => 'none',
            'excludeCredentials' => $existingCredentials,
        ];
    }

    /**
     * Verify WebAuthn registration response and save credential.
     */
    public function verifyRegistration(User $user, array $response, string $deviceName = 'Biometric Device'): StaffBiometricCredential
    {
        $expectedChallenge = session('webauthn_register_challenge');
        if (! $expectedChallenge) {
            throw new Exception('WebAuthn registration session expired or missing challenge.');
        }

        $clientDataJSON = $this->base64UrlDecode($response['clientDataJSON'] ?? '');
        $clientData = json_decode($clientDataJSON, true);

        if (! $clientData || ($clientData['type'] ?? '') !== 'webauthn.create') {
            throw new Exception('Invalid clientData type for WebAuthn registration.');
        }

        if (($clientData['challenge'] ?? '') !== $expectedChallenge) {
            throw new Exception('WebAuthn challenge mismatch.');
        }

        // Clear challenge
        session()->forget('webauthn_register_challenge');

        $rawCredentialId = $response['id'] ?? '';
        if (empty($rawCredentialId)) {
            throw new Exception('Missing credential ID.');
        }

        // Parse attestationObject using CBOR decoder
        $attestationObjectRaw = $this->base64UrlDecode($response['attestationObject'] ?? '');
        $parsedAuthData = $this->parseAttestationObject($attestationObjectRaw);

        $publicKeyPem = $parsedAuthData['publicKeyPem'];
        $aaguid = $parsedAuthData['aaguid'] ?? null;
        $counter = $parsedAuthData['counter'] ?? 0;

        return StaffBiometricCredential::create([
            'user_id' => $user->id,
            'name' => $deviceName ?: 'Biometric Device',
            'credential_id' => $rawCredentialId,
            'public_key' => $publicKeyPem,
            'counter' => $counter,
            'aaguid' => $aaguid,
            'attestation_type' => 'none',
            'transports' => $response['transports'] ?? ['internal'],
            'device_type' => $response['authenticatorAttachment'] ?? 'platform',
            'last_used_at' => now(),
            'is_active' => true,
        ]);
    }

    /**
     * Generate WebAuthn authentication options for punch-in / punch-out.
     */
    public function generateAuthOptions(User $user): array
    {
        $challenge = $this->generateChallenge();
        session(['webauthn_auth_challenge' => $challenge]);

        $allowCredentials = $user->biometricCredentials()
            ->where('is_active', true)
            ->get()
            ->map(fn ($cred) => [
                'type' => 'public-key',
                'id' => $cred->credential_id,
            ])
            ->toArray();

        return [
            'challenge' => $challenge,
            'timeout' => 60000,
            'rpId' => $this->getRelyingParty()['id'],
            'allowCredentials' => $allowCredentials,
            'userVerification' => 'preferred',
        ];
    }

    /**
     * Verify WebAuthn assertion signature during punch-in / punch-out.
     */
    public function verifyAuthentication(User $user, array $response): StaffBiometricCredential
    {
        $expectedChallenge = session('webauthn_auth_challenge');
        if (! $expectedChallenge) {
            throw new Exception('WebAuthn authentication session expired or missing challenge.');
        }

        $clientDataJSON = $this->base64UrlDecode($response['clientDataJSON'] ?? '');
        $clientData = json_decode($clientDataJSON, true);

        if (! $clientData || ($clientData['type'] ?? '') !== 'webauthn.get') {
            throw new Exception('Invalid clientData type for WebAuthn authentication.');
        }

        if (($clientData['challenge'] ?? '') !== $expectedChallenge) {
            throw new Exception('WebAuthn challenge mismatch.');
        }

        session()->forget('webauthn_auth_challenge');

        $credentialId = $response['id'] ?? '';
        $credential = $user->biometricCredentials()
            ->where('credential_id', $credentialId)
            ->where('is_active', true)
            ->first();

        if (! $credential) {
            throw new Exception('Biometric credential not found or has been revoked.');
        }

        $authenticatorDataRaw = $this->base64UrlDecode($response['authenticatorData'] ?? '');
        if (strlen($authenticatorDataRaw) < 37) {
            throw new Exception('Invalid authenticatorData length.');
        }

        // Verify User Present flag (bit 0 of flags byte at offset 32)
        $flags = ord($authenticatorDataRaw[32]);
        $userPresent = ($flags & 0x01) !== 0;
        if (! $userPresent) {
            throw new Exception('User presence not detected by authenticator.');
        }

        // Extract counter (bytes 33..36 uint32 big-endian)
        $counter = unpack('N', substr($authenticatorDataRaw, 33, 4))[1];

        // Signature covers authenticatorData + SHA256(clientDataJSON)
        $signatureRaw = $this->base64UrlDecode($response['signature'] ?? '');
        $signedData = $authenticatorDataRaw.hash('sha256', $clientDataJSON, true);

        // Verify signature with OpenSSL (try direct DER and raw P1363 formats)
        $verified = openssl_verify($signedData, $signatureRaw, $credential->public_key, OPENSSL_ALGO_SHA256);
        if ($verified !== 1 && strlen($signatureRaw) === 64) {
            $derSig = $this->rawSignatureToDer($signatureRaw);
            $verified = openssl_verify($signedData, $derSig, $credential->public_key, OPENSSL_ALGO_SHA256);
        }

        if ($verified !== 1) {
            Log::warning("WebAuthn signature verification failed for user {$user->id} on credential {$credential->id}. Return code: {$verified}");
            throw new Exception('Cryptographic biometric signature verification failed.');
        }

        // Update counter and last used
        $credential->update([
            'counter' => $counter,
            'last_used_at' => now(),
        ]);

        return $credential;
    }

    /**
     * Unpack and extract COSE public key from attestation object to standard PEM.
     */
    protected function parseAttestationObject(string $attestationObjectRaw): array
    {
        try {
            $decoder = new CborDecoder($attestationObjectRaw);
            $attestation = $decoder->decode();
            $authData = $attestation['authData'] ?? $attestationObjectRaw;
        } catch (Exception $e) {
            $authData = $attestationObjectRaw;
        }

        // If authData was wrapped or fallback
        if (is_array($authData)) {
            $authData = $attestationObjectRaw;
        }

        // Ensure minimum length (37 bytes header + 16 bytes AAGUID + 2 bytes credIdLen)
        if (strlen($authData) < 55) {
            throw new Exception('Invalid authData structure in attestation object.');
        }

        $aaguid = bin2hex(substr($authData, 37, 16));
        $credIdLen = unpack('n', substr($authData, 53, 2))[1];
        $coseKeyRaw = substr($authData, 55 + $credIdLen);

        $publicKeyPem = $this->coseToPem($coseKeyRaw);

        return [
            'publicKeyPem' => $publicKeyPem,
            'aaguid' => $aaguid,
            'counter' => 0,
        ];
    }

    /**
     * Convert COSE public key into PEM format for OpenSSL.
     */
    public function coseToPem(string $coseKeyRaw): string
    {
        try {
            $decoder = new CborDecoder($coseKeyRaw);
            $coseMap = $decoder->decode();

            if (is_array($coseMap)) {
                $kty = $coseMap[1] ?? null;

                // EC2 key (ECDSA P-256)
                if ($kty === 2) {
                    $x = $coseMap[-2] ?? null;
                    $y = $coseMap[-3] ?? null;

                    if ($x && $y && strlen($x) === 32 && strlen($y) === 32) {
                        $uncompressedPoint = "\x04".$x.$y;
                        $derHeader = hex2bin('3059301306072a8648ce3d020106082a8648ce3d030107034200');
                        $der = $derHeader.$uncompressedPoint;

                        return "-----BEGIN PUBLIC KEY-----\n".chunk_split(base64_encode($der), 64, "\n")."-----END PUBLIC KEY-----\n";
                    }
                }

                // RSA key (RS256)
                if ($kty === 3) {
                    $n = $coseMap[-1] ?? null;
                    $e = $coseMap[-2] ?? null;

                    if ($n && $e) {
                        return $this->rsaModExpToPem($n, $e);
                    }
                }
            }
        } catch (Exception $e) {
            Log::warning('COSE decoder error: '.$e->getMessage());
        }

        // Direct PEM fallback
        if (str_contains($coseKeyRaw, 'BEGIN PUBLIC KEY')) {
            return $coseKeyRaw;
        }

        throw new Exception('Unable to parse biometric public key from authenticator.');
    }

    /**
     * Convert RSA Modulus & Exponent to SubjectPublicKeyInfo PEM.
     */
    protected function rsaModExpToPem(string $n, string $e): string
    {
        $n = ltrim($n, "\x00");
        $e = ltrim($e, "\x00");

        if (ord($n[0]) >= 0x80) {
            $n = "\x00".$n;
        }
        if (ord($e[0]) >= 0x80) {
            $e = "\x00".$e;
        }

        $nDer = "\x02".$this->asn1Length(strlen($n)).$n;
        $eDer = "\x02".$this->asn1Length(strlen($e)).$e;
        $rsaPubKey = "\x30".$this->asn1Length(strlen($nDer.$eDer)).$nDer.$eDer;

        $bitString = "\x03".$this->asn1Length(strlen($rsaPubKey) + 1)."\x00".$rsaPubKey;
        $algId = hex2bin('300d06092a864886f70d0101010500'); // rsaEncryption, null
        $spki = "\x30".$this->asn1Length(strlen($algId.$bitString)).$algId.$bitString;

        return "-----BEGIN PUBLIC KEY-----\n".chunk_split(base64_encode($spki), 64, "\n")."-----END PUBLIC KEY-----\n";
    }

    /**
     * Convert IEEE P1363 (r || s) 64-byte signature to ASN.1 DER sequence.
     */
    protected function rawSignatureToDer(string $sig): string
    {
        if (strlen($sig) !== 64) {
            return $sig;
        }

        $r = substr($sig, 0, 32);
        $s = substr($sig, 32, 32);

        $r = ltrim($r, "\x00");
        $s = ltrim($s, "\x00");

        if (empty($r) || ord($r[0]) >= 0x80) {
            $r = "\x00".$r;
        }
        if (empty($s) || ord($s[0]) >= 0x80) {
            $s = "\x00".$s;
        }

        $rDer = "\x02".$this->asn1Length(strlen($r)).$r;
        $sDer = "\x02".$this->asn1Length(strlen($s)).$s;
        $seq = $rDer.$sDer;

        return "\x30".$this->asn1Length(strlen($seq)).$seq;
    }

    protected function asn1Length(int $length): string
    {
        if ($length < 128) {
            return chr($length);
        }

        $temp = '';
        while ($length > 0) {
            $temp = chr($length & 0xFF).$temp;
            $length >>= 8;
        }

        return chr(0x80 | strlen($temp)).$temp;
    }

    /**
     * Helper Base64URL encode.
     */
    public function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Helper Base64URL decode.
     */
    public function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/')) ?: '';
    }
}

/**
 * Lightweight & Robust Recursive CBOR Decoder (RFC 8949)
 */
class CborDecoder
{
    private int $offset = 0;

    public function __construct(private string $data)
    {
        $this->offset = 0;
    }

    public function decode(): mixed
    {
        if ($this->offset >= strlen($this->data)) {
            return null;
        }

        $initialByte = ord($this->data[$this->offset++]);
        $majorType = $initialByte >> 5;
        $additionalInfo = $initialByte & 0x1F;

        $length = $this->readLength($additionalInfo);

        return match ($majorType) {
            0 => $length,                     // Unsigned integer
            1 => -1 - $length,                // Negative integer
            2, 3 => $this->readBytes($length), // 2: byte string, 3: text string (utf-8)
            4 => $this->readArray($length),   // Array
            5 => $this->readMap($length),     // Map
            6 => $this->decode(),             // Semantic tag (skip tag and decode value)
            7 => match ($additionalInfo) {
                20 => false,
                21 => true,
                22 => null,
                default => null,
            },
            default => null,
        };
    }

    private function readLength(int $additionalInfo): int
    {
        if ($additionalInfo < 24) {
            return $additionalInfo;
        }

        return match ($additionalInfo) {
            24 => ord($this->data[$this->offset++]),
            25 => unpack('n', $this->readBytes(2))[1],
            26 => unpack('N', $this->readBytes(4))[1],
            27 => unpack('J', $this->readBytes(8))[1],
            default => 0,
        };
    }

    private function readBytes(int $length): string
    {
        $bytes = substr($this->data, $this->offset, $length);
        $this->offset += $length;

        return $bytes;
    }

    private function readArray(int $length): array
    {
        $arr = [];
        for ($i = 0; $i < $length; $i++) {
            $arr[] = $this->decode();
        }

        return $arr;
    }

    private function readMap(int $length): array
    {
        $map = [];
        for ($i = 0; $i < $length; $i++) {
            $key = $this->decode();
            $val = $this->decode();
            $map[$key] = $val;
        }

        return $map;
    }
}
