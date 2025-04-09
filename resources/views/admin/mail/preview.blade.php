@extends('admin.layouts.popup-layout')

@section('addStyle')
@endsection

@section('contents')
    <div class="sub-tit-wrap" style="margin-bottom: 50px;">
        <h3 class="sub-tit">Preview</h3>
    </div>

    @include($mailConfig['admin_template'][$mail->template]['path'])
@endsection

@section('addScript')
@endsection
