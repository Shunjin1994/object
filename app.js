//お決まり
//DOMが作り終わったあとに中身の処理実行
$(function(){
    //ここに処理の中身を書く
    //今回はタイトルのフォームが入力されたらsubmitボタンが押せるようにする
    //それまでsubmitボタンはdisabledにしておく

    //1.タイトルフォームが入力された場合のイベントをセットする    focus change
    $('.js-form-validate').on('keyup', function () {
      console.log('.js-form-validate');
        //2.タイトルフォームの中身(value)を取得して中身が入っているか確認
        //if文、なにか値があればtrue、空ならfalse
        var email = document.getElementById("email").value;
        var password = document.getElementById("password").value;
        var password_re = document.getElementById("password_re").value;

        var checkResult = true;

        if(password.length <= 5){
            checkResult = false;
        }
        
        if(checkResult){
            //3.中身が入っているならdisabledをはずす
            $('.js-disabled-submit').prop('disabled', false);
        }else{
            //4.中身が入っていなければdisabledにする
            $('.js-disabled-submit').prop('disabled', true);
        }
    });

    // $('.js-blur-valid-email').on('blur',function (e) {

    //     // コールバック関数内では、thisはajax関数自体になってしまうため、
    //     // ajax関数内でイベントのthisを使いたいなら変数に保持しておく
    //     var $that = $(this);
    
    //     // // Ajaxを実行する
    //     $.ajax({
    //       type: 'post',
    //       url: 'ajax.php',
    //       dataType: 'json', // 必ず指定すること。指定しないとエラーが出る＆返却値を文字列と認識してしまう
    //       data: {
    //         email: $(this).val()
    //       }
    //     }).then(function(data) {
    //       console.log(data);

    //       if(data){
    //         console.log(data);
    
    //         // フォームにメッセージをセットし、背景色を変更する
    //         if(data.errorFlg){
    //           $('.js-set-msg-email').addClass('is-error');
    //           $('.js-set-msg-email').removeClass('is-success');
    //           $that.addClass('is-error');
    //           $that.removeClass('is-success');
    //         }else{
    //           $('.js-set-msg-email').addClass('is-success');
    //           $('.js-set-msg-email').removeClass('is-error');
    //           $that.addClass('is-success');
    //           $that.removeClass('is-error');
    //         }
    //         $('.js-set-msg-email').text(data.msg);
    //       }
    //     });
    //   });
});