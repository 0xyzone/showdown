@extends('errors.layout')

@section('title', '419 - Session Disconnected')
@section('code', '419')
@section('subhead', 'SESSION DISCONNECTED // AFK TIMEOUT')
@section('message_title', 'SESSION TOKEN EXPIRED')
@section('message_body', 'AFK detection triggered! Your security token went idle for too long and surrendered its session handshake. Please hit reconnect to re-authenticate.')
