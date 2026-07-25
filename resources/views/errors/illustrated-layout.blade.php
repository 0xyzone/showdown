@extends('errors.layout')

@section('title', ($code ?? '404') . ' - Game Event')
@section('code', $code ?? '404')
@section('subhead', 'ESPORTS SYSTEM ALERT // EXCEPTION CAUGHT')
@section('message_title', strtoupper($title ?? 'SYSTEM EVENT DETECTED'))
@section('message_body', $message ?? 'An unexpected tactical anomaly occurred on the match server. Please return to base or retry your command.')
