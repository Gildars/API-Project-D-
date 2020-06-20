<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Anonymous Chat App</title>

    {{-- Script hosted on running laravel-echo-server --}}
    <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
</head>
<body>
<div class="content">
    <div id="app">
        <chat-box></chat-box>
    </div>
</div>
<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
