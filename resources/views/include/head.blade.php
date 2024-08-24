<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('public/img/logo_white.png')}}" />
<link href="{{ asset('public/css/font.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('public/all.css') }}">
<link rel="stylesheet" href="{{ asset('public/dist/css/theme.css') }}">
<link rel="stylesheet" href="{{ asset('public/plugins/fontawesome-free/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/plugins/icon-kit/dist/css/iconkit.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/plugins/ionicons/dist/css/ionicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/plugins/jquery-toast-plugin/dist/jquery.toast.min.css') }}">
@stack('head')
<link rel="stylesheet" href="{{ asset('public/css/style.css') }}">
<script src="{{ asset('public/js/app.js') }}"></script>
<style>


.wrapper .header-top[header-theme="dark"] {
  background: #2f2f2f;
}
.wrapper .page-wrap .app-sidebar{
    background-color:#2f2f2f !important;
}
.wrapper .page-wrap .app-sidebar .sidebar-content .nav-container .navigation-main .nav-item a{
    color:#bfbfbf !important;
}
.wrapper .page-wrap .app-sidebar .sidebar-header {
  background: #2f2f2f !important;
}
.wrapper .page-wrap .footer {
  background: #2f2f2f !important;
}
.footer span{
    color:#bfbfbf !important;
}
.wrapper .page-wrap .footer{
    padding-left:0px !important;
}
/*@keyframes blink {*/
/*  0%, 100% {*/
/*      opacity: 1;*/
/*  }*/

/*  10% {*/
/*      opacity: 0;*/
/*  }*/
/*}*/
/*.blink {*/
/*    animation: blink 2s infinite;*/
/*    color: #ffc800;*/
/*} */
.notifications-wrap h5{
    text-align: left;
    font-size: 14px;
    padding: 5px;
    border-bottom: 1px solid #dedede;    
}
@media (max-width: 767px) {
    .sm-dnone {
        display: none !important;
    }
    .current_user_locator .text_name {
        font-size: 12px !important;
        display: inline-block;
        vertical-align: middle;
    }
    .current_user_locator .user_icon {
        font-size: 20px;
        margin-right: 5px;
        display: inline-block;
        vertical-align: middle;
    }
}
.loader {
  margin:auto;
  width: 32px;
  aspect-ratio: 1;
  display: grid;
  border: 4px solid #0000;
  border-radius: 50%;
  border-color: #226bc5 #0000;
  animation: l16 1s infinite linear;
}
.loader::before,
.loader::after {    
  content: "";
  grid-area: 1/1;
  margin: 2px;
  border: inherit;
  border-radius: 50%;
}
.loader::before {
  border-color: #f03355 #0000;
  animation: inherit; 
  animation-duration: .5s;
  animation-direction: reverse;
}
.loader::after {
  margin: 8px;
}
@keyframes l16 { 
  100%{transform: rotate(1turn)}
}              
</style>
