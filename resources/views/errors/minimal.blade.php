@extends('errors.layout')

@section('title', ($exception->getStatusCode() ?? '404') . ' - Game Event')
@section('code', $exception->getStatusCode() ?? '404')
@section('subhead', 'ESPORTS SYSTEM ALERT // EXCEPTION CAUGHT')
@section('message_title', strtoupper($exception->getMessage() ?: 'SYSTEM EVENT DETECTED'))
@section('message_body', $exception->getMessage() ?: 'An unexpected tactical anomaly occurred on the match server. Please return to base or retry your command.')
