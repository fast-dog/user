<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 15.08.2017
 * Time: 10:42
 */
?>
<link rel="stylesheet" href="<?=url($theme . 'plugins/emojionearea/dist/emojionearea.css')?>">
<script src="{{$theme}}plugins/emojionearea/dist/emojionearea.min.js"></script>
<script src="{{$theme}}js/jquery.slimscroll.min.js"></script>
<script src="{{$theme}}js/jquery.ui.widget.js" type="text/javascript"></script>
<script src="{{$theme}}js/jquery.fileupload.js" type="text/javascript"></script>
<script src="{{$theme}}js/chat.js"></script>
{!! talk_live(['user'=>["id"=>$user->id, 'callback'=>['chat.newEvents']]]) !!}
<style type="text/css">
    .chat-mess {
        padding-top: 5px;
        padding-bottom: 10px
    }

    .chat-dateline {
        text-align: center;
    }

    .hidden {
        display: none;
    }

    .attachment {
        max-height: 120px;
        max-width: 120px;
        overflow: hidden;
        position: relative;
    }

    .attachment > .trash {
        position: absolute;
        top: 5px;
        right: 5px;
    }
</style>
