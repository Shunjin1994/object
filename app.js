//お決まり
//DOMが作り終わったあとに中身の処理実行
$(function(){
    //ここに処理の中身を書く
    //今回はタイトルのフォームが入力されたらsubmitボタンが押せるようにする
    //それまでsubmitボタンはdisabledにしておく

    //1.タイトルフォームが入力された場合のイベントをセットする    focus change
    $('.js-form-validate').on('keyup', function () {
        //2.タイトルフォームの中身(value)を取得して中身が入っているか確認
        //if文、なにか値があればtrue、空ならfalse
        var email = document.getElementById("email").value;
        var password = document.getElementById("password").value;
        var password_re = document.getElementById("password_re").value;

        // var result = 1;
        var checkResult = true;
        var e_regexp = /^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]{1,}\.[A-Za-z0-9]{1,}$/;
        var p_regexp = /^(?=.*?[a-z])(?=.*?\d)[a-z\d]{6,20}$/i;


        if(!email || e_regexp.test(email)){
            $('.js-set-msg-email').removeClass('is-error');
            $(this).removeClass('is-error');
            $('.js-set-msg-email').text("");
        }else{
            checkResult = false;
            $('.js-set-msg-email').addClass('is-error');
            $(this).addClass('is-error');
            $('.js-set-msg-email').text("メールアドレスの形式ではありません");
        }

        if (!password || password.length >= 6 || p_regexp.test(password)) {
            $('.js-set-msg-password').removeClass('is-error');
            $(this).removeClass('is-error');
            $('.js-set-msg-password').text("");
        }else{
            checkResult = false;
            $('.js-set-msg-password').addClass('is-error');
            $(this).addClass('is-error');
            $('.js-set-msg-password').text("半角英数字6文字以上ではありません");
        }

        if (!password_re || password_re === password) {
            $('.js-set-msg-password_re').removeClass('is-error');
            $(this).removeClass('is-error');
            $('.js-set-msg-password_re').text("");
        }else{
            $('.js-set-msg-password_re').addClass('is-error');
            $(this).addClass('is-error');
            $('.js-set-msg-password_re').text("パスワードと一致していません");
        }
        
        if(checkResult){
            //3.中身が入っているならdisabledをはずす
            $('.js-disabled-submit').prop('disabled', false);
        }else{
            //4.中身が入っていなければdisabledにする
            $('.js-disabled-submit').prop('disabled', true);
        }
    });
});