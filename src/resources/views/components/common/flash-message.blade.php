{{--初期設定--}}
@props(['status' => 'info'])

{{--フラッシュメッセージの背景色を切り分ける--}}
@php
    if (session('status') === 'info') {
        $bgColor = 'bg-blue-300';
    }
    if (session('status') === 'alert') {
        $bgColor = 'bg-red-500';
 } @endphp

{{-- メッセージを表示する --}}
@if (session('message'))
    <div class="{{ $bgColor }} p-2 mb-3 text-white rounded">
        {{ session('message') }}
    </div>
@endif
