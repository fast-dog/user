<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 15.08.2017
 * Time: 18:08
 */
?>
<li class="thumbnail attachment">
    @if($image['delete'])
        <a href="javascript:void(0)" class="trash btn btn-xs btn-danger"
           data-action="delete-attach"
           data-id="{!! $image['id'] !!}">
            <i class="fa fa-trash-o"></i></a>
    @endif
    <a href="{!! $image['path'] !!}" class="fancybox">
        <img class="img-responsive" src="{!! $image['thumbs']['file'] !!}" alt="">
    </a>
</li>
