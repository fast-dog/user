<?php
/**
 * Created by PhpStorm.
 * User: dg
 * Date: 02.05.2017
 * Time: 19:35
 * @var $messageManager App\Modules\Users\Entity\MessageManager
 * @var $recipient \App\Modules\Users\User
 * @var $user \App\Modules\Users\User
 */

?>
@extends('public.000.layouts.user_cabinet')

@section('meta')
    @include('public.000.partials.meta',['metadata'=>$metadata])
@endsection

@section('script')
    <!-- <script src="{{$theme}}js/mix.js"></script>-->
    @include('public.001.modules.users.cabinet.messages.partials.chat-assets')
@endsection

@section('script_ready')
    chat.init(<?=$user->id?>,<?=$disabled_sent == false?>);
@endsection

@section('user_content')
    <div class="profile-body">
        @if(\Session::has('message'))
            <div class="alert alert-success fade in">
                {{\Session::get('message')}}
            </div>
        @endif
        @if(!$conversation)
            @if(count($messages) == 0)
                @include('public.000.partials.empty-messages')
            @else
                <div class="panel panel-profile">
                    <div class="panel-heading overflow-h">
                        <h2 class="panel-title heading-sm pull-left"><i class="fa fa-comments"></i>
                            @trans(Входящие сообщения)
                        </h2>
                    </div>
                    <div class="panel-body margin-bottom-50">
                        @foreach($messages as $item)
                            @include('public.001.modules.users.cabinet.messages.partials.message-inbox')
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        @if($conversation)
            <div class="panel panel-profile">
                <div class="panel-heading overflow-h">
                    <h2 class="panel-title heading-sm pull-left"><i class="fa fa-comments"></i>
                        {{trans('public.Диалог с пользователем: :name',['name'=>$recipient->getName()])}}</h2>
                </div>
                <div class="panel-body margin-bottom-50" style="position: relative">
                    <div id="mask"
                         style="display: none;background: #9bbc33; opacity: 0.35; z-index: 1000; position:absolute; left: 0; top: 0; right: 0; bottom: 0;"></div>
                    <div class="input-group">
                        <input type="text" class="form-control"
                               data-action="search-message" placeholder="@trans(Искать в недавних сообщениях)"
                               name="search">
                        <span class="input-group-btn">
                           <!-- <button class="btn btn-success"
                                    data-original-title="@trans(Загрузить всю историю)"
                                    data-toggle="tooltip" type="button">
                                <i class="fa fa-history"></i>
                            </button> -->
                            <button class="btn btn-danger"
                                    data-conversation_id="<?=$conversation_id?>"
                                    data-action="clear-messages"
                                    data-original-title="@trans(Очистить чат)"
                                    data-toggle="tooltip" type="button">
                                <i class="fa fa-trash-o"></i>
                            </button>
						</span>
                    </div>
                    <div id="chat_body" style="height: 600px;"
                         data-empty_text="@trans(Начните диалог отправив сообщение.)"
                         data-attach-url="{{url('/cabinet/attach')}}"
                         data-pages="{{$conversation_pages}}"
                         data-id="{{$conversation_id}}"
                         data-page="1">
                        @if($conversation->messages->isEmpty())
                            <br/>
                            <div class="alert alert-success">
                                @trans(Начните диалог отправив сообщение.)
                            </div>
                        @else
                            <?

                            ?>
                            @foreach($messages as $message)
                                <?  if ($message['dateline']): ?>
                                <div class="chat-dateline">
                                <span class="text-highlights text-highlights-green rounded-2x">
                                    {{$message['dateline_text']}}</span>
                                </div>
                                <? endif;?>
                                @include('public.001.modules.users.cabinet.messages.partials.message')
                            @endforeach
                        @endif
                        <div class="alert alert-warning" data-action="error"
                             style="position: absolute; bottom: 0; left: 0; right: 0; display: none">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <p></p>
                        </div>
                    </div>

                    <form action="{{url('cabinet/new-message')}}" method="post" name="send-new-message"
                          class="margin-bottom-20">
                        <div class="input-group">
                            <input type="text" class="form-control"
                                   name="message"
                                   <?=($disabled_sent) ? 'disabled' : ''?>
                                   data-action="emoji">
                            <span class="input-group-btn">
							<button class="btn btn-default"
                                    <?=($disabled_sent) ? 'disabled' : ''?>
                                    data-action="upload-file"
                                    type="button">
                                <i class="fa fa-paperclip"></i>
                            </button>
                                <span class="hidden"><input type="file" name="attach"/></span>
                            <button class="btn btn-success" type="button"
                                    <?=($disabled_sent) ? 'disabled' : ''?>
                                    data-action="send-new-message">
                                <i class="fa fa-share"></i>
                            </button>
						</span>
                        </div>
                        <input type="hidden" name="recipient" value="{{$recipient->id}}">
                        @if($conversation_id)
                            <input type="hidden" name="conversation_id" value="{{$conversation_id}}">
                        @endif
                    </form>
                    <ul class="list-inline img-uploaded empty">
                        @foreach($attach as $image)
                            <? $image['delete'] = true;?>
                            @include('public.001.modules.users.cabinet.messages.partials.image-attachment')
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="hidden">
                @include('public.001.modules.users.cabinet.messages.partials.message-template')
            </div>
        @endif
    </div>
@endsection