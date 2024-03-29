@php
$chatroomDetails = json_decode($chatroom, true);
$chatroomUniqid = $chatroomDetails['unique_id'];
$chatroomDetails['description'] = substr($chatroomDetails['description'],30);
$chatroomUserDetails = json_decode($chatroomUser, true);
@endphp

@extends('login.layout.app', ['activePage' => '', 'title' => $chatroomDetails['name'], 'currentChatroom' => $chatroomUniqid ])

@section('content')
<div class="content" style="margin-top: 30px;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card card-stats">
                    <div class="card-header card-header-info card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">chat</i>
                        </div>
                        <div class="card-category chatroom-info-left">
                            <h4>{{ $chatroomDetails['name'] }}</h4>
                            <div id="chatroomParticipate"></div>
                        </div>
                        {{-- <div class="card-category chatroom-info-right" style="height: 0;">
                            {{ $chatroomDetails['description'] }}
                        </div> --}}
                        <div class="card-category chatroom-info-right">
                            <a href="{{ route('login.chatroom.setting', ['unique_id'=>$chatroomUniqid]) }}">
                                <i class="fas fa-cog"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body message-body" id="message-body">
                        Loading...

                    </div>
                    <div class="card-footer row">
                        <div class="col-12">
                            {{-- <form class="form col-12" id="message"> --}}
                            @csrf
                            <div class="form-group">
                                <input type="text" class="form-control col-10" id="inputMessage"
                                    placeholder="message..." autofocus>
                            </div>
                            {{-- </form> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection


@push('js')
{{-- socket --}}
<script>
    chatroomUniqid = '{{ $chatroomUniqid }}';
    chatroomType = '{{ $chatroomDetails["type"] }}';
    chatroomUser = {!! $chatroomUser !!};

    // for socket
    var currentChatroomUser = [];
    var responseFromDB;
    var typingMessage = false;
    chatroomTypingParticipate = [];

    // for html output (process participant info in JS)
    var participantInfo = [];
    $.each(chatroomUser, function(i, item) {
        // for socket
        var temp = {};
        temp['unique_id'] = item['unique_id'];
        temp['side'] = item['side'];
        if(item['unique_id'] == myUniqid){
            myName = item['name'];
            mySide = item['side'];
        }
        currentChatroomUser.push(temp);
        // for html output
        participantInfo[item['unique_id']] = item;
    });
    updateChatroomParticipate();

    messageSend = {socketType: "initChatroom",
                        id: "{{ session()->getId() }}",
                        chatroomUniqid: chatroomUniqid,
                        chatroomType: chatroomType,
                        myUniqid: myUniqid,
                        myName: myName,
                        mySide: mySide,
                        currentChatroomUser: currentChatroomUser,
                        chatroomTypingParticipate: chatroomTypingParticipate};
</script>

{{-- output message to html --}}
<script>
    function socketNewChatroomMessage(response){
        try {
            outputMessage(response);
            $("#message-body").animate({ scrollTop: $("#message-body")[0].scrollHeight}, 1000);
        } catch (error) {
            location.reload();                    
        }
        $.get("{{ route('backend.chatroom.messageSeen', ['unqiue_id'=>''] )}}" + "/" + response["messageUniqid"]);
    }

    function messageLeft(tempMessage){
        var name = participantInfo[tempMessage['senderUniqid']]['name'];
        var profilePic = participantInfo[tempMessage['senderUniqid']]['profile_picture'];
        if(profilePic == null){
            var initials = participantInfo[tempMessage['senderUniqid']]['initials'];
            profilePic = '<img src="https://placehold.it/50/FA6F57/fff&amp;text=' + initials + '" alt="' + initials + '" class="rounded-circle mx-auto d-block">'
        }

        var tempHtml = '';
        tempHtml += '<div class="row message-item">';
        tempHtml += '<div class="col-1" id="message:' + tempMessage["messageUniqid"] +'">';
        tempHtml += profilePic;
        tempHtml += '</div><div class="col-9 message-left">';
        tempHtml += tempMessage['message'] + '<br><small class="message-time">'+ name + ' @'+ tempMessage['messageUpdateAt'] + '</small>';
        tempHtml += '</div></div>';
        return tempHtml;
        
    }
    function messageRight(tempMessage){
        var name = participantInfo[tempMessage['senderUniqid']]['name'];
        var profilePic = participantInfo[tempMessage['senderUniqid']]['profile_picture'];
        if(profilePic == null){
            var initials = participantInfo[tempMessage['senderUniqid']]['initials'];
            if (participantInfo[tempMessage['senderUniqid']]['currentUser'] == true) {
                profilePic = '<img src="https://placehold.it/50/1169F7/fff&amp;text=' + initials + '" alt="' + initials + '" class="rounded-circle mx-auto d-block">'
            }else{
                profilePic = '<img src="https://placehold.it/50/FA6F57/fff&amp;text=' + initials + '" alt="' + initials + '" class="rounded-circle mx-auto d-block">'
            }
        }

        var tempHtml = '';
        tempHtml += '<div class="row message-item">';
        tempHtml += '<div class="col-2"></div><div class="col-9 message-right" id="message:' + tempMessage["messageUniqid"] +'">';
        tempHtml += tempMessage['message'] + '<br><small class="message-time">'+ name + ' @'+ tempMessage['messageUpdateAt'] + '</small>';
        tempHtml += '</div><div class="col-1">';
        tempHtml += profilePic;
        tempHtml += '</div></div>';
        return tempHtml;
    }
    
    function outputMessage(message){
        var outputHtml = '';
        if(message['messageSide'] == 'myMessage'){
            outputHtml += messageRight(message);
        }else if(message['messageSide'] == 'sameSide'){
            outputHtml += messageRight(message);
        }else{
            //oppositeSide
            outputHtml += messageLeft(message);
        }
        $('#message-body').append(outputHtml);
    }

    function checkType(){
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        if(urlParams.get('type') ==='new'){
            messageSend['socketType'] = "notiNewInvitation";
            Socket.send(JSON.stringify(messageSend));
        }
    }

    $(function() {
        // style adjust
        $("#pageTitle").hide();    

        $('.message-body').height($(window).height() * 0.6);
        $( "footer" ).remove("footer");
        $('a.navbar-brand').remove();

        Socket.onopen = function(event){
            console.log(Socket.readyState);
            Socket.send(JSON.stringify(messageSend));
            checkSocketTypingStatus();
            checkType();
        }

        message = {!! $message !!};
        $('#message-body').html('');
        $.each(message, function(i, item) {
            outputMessage(item);
        });
        $("#message-body").animate({ scrollTop: $("#message-body")[0].scrollHeight}, 1000);

        $('#inputMessage').on('keyup', function (e) {
            if(e.which === 13){
                // send new chatroom message
                $(this).attr("disabled", "disabled");
                message = $('#inputMessage').val()
                messageSend['socketType'] = "sendMessage";
                messageSend['message'] = message;
                $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('ajax.chatroom.newMessage') }}",
                    method: 'post',
                    data: {
                        // to DB
                        chatroomUniqid: chatroomUniqid, 
                        message: message
                    },
                    success: function(response){
                        responseFromDB =  response['output'];
                        messageSend['messageUniqid'] = responseFromDB['messageUniqid'];
                        messageSend['messageUpdateAt'] = responseFromDB['messageUpdateAt'];
                        Socket.send(JSON.stringify(messageSend));

                        console.log('messageSend to socket');
                        console.log(messageSend);

                        // stop typing
                        messageSend['socketType'] = "stopTypingMessage";
                        Socket.send(JSON.stringify(messageSend));
                        typingMessage = false;
                    }
                });
                typingMessage = false;
                $(this).val('');
                $(this).removeAttr("disabled");
                $(this).focus();
            }else{
                if(!typingMessage){
                    // start to typing
                    messageSend['socketType'] = "startTypingMessage";
                    Socket.send(JSON.stringify(messageSend));
                    typingMessage = true;
                }else if($('#inputMessage').val().length == 0){
                    // stop typing
                    messageSend['socketType'] = "stopTypingMessage";
                    Socket.send(JSON.stringify(messageSend));
                    typingMessage = false;
                }
            }
        });
    });

</script>

@endpush