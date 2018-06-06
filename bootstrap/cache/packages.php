<?php return array (
  'fideloper/proxy' => 
  array (
    'providers' => 
    array (
      0 => 'Fideloper\\Proxy\\TrustedProxyServiceProvider',
    ),
  ),
  'laravel/tinker' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Tinker\\TinkerServiceProvider',
    ),
  ),
  'maatwebsite/excel' => 
  array (
    'providers' => 
    array (
      0 => 'Maatwebsite\\Excel\\ExcelServiceProvider',
    ),
    'aliases' => 
    array (
      'Excel' => 'Maatwebsite\\Excel\\Facades\\Excel',
    ),
  ),
  'pusher/pusher-http-laravel' => 
  array (
    'providers' => 
    array (
      0 => 'Pusher\\Laravel\\PusherServiceProvider',
    ),
    'aliases' => 
    array (
      'Pusher' => 'Pusher\\Laravel\\Facades\\Pusher',
    ),
  ),
);