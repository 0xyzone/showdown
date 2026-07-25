@extends('errors.layout')

@section('title', '429 - APM Overheat Rate Limit')
@section('code', '429')
@section('subhead', 'APM OVERHEAT // RATE LIMIT TRIGGERED')
@section('message_title', 'TOO MANY REQUESTS')
@section('message_body', 'Woah, trigger-finger! Your APM (Actions Per Minute) surpassed our server packet limit. Take a breather, hydrate, and try your action again in a few seconds.')
