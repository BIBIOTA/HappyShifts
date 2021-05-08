<template>
  <header>
    <div class="title">
      <a href="./index.html">
        <h1 class="firstwords">
          Happy&nbsp;
        </h1>
        <h1 class="secwords">
          Shifts&nbsp;
        </h1>
        <h1 class="thirdwords">
          班表管理
        </h1>
      </a>
    </div>
    <nav>
      <div class="user" v-show="haslogin"><h2>登入: {{username}}</h2></div>
      <div class="sign memberbtn" @click="open('signup')" v-show="haslogin === false">
        <h2>註冊</h2>
      </div>
      <div class="log memberbtn" @click="open('login')" v-show="haslogin === false">
        <h2>登入</h2>
      </div>
      <div class="memberbtn" @click="logout" v-show="haslogin">
        <h2>登出</h2>
      </div>
    </nav>
  </header>
  <div class="member_pop" :class="{'on': pop}">
    <div class="member_pop_in">

      <div class="card log" v-show="pop_card === 'login'">
        <a @click="close">
          <i class="far fa-times-circle"></i>
        </a>
        <h1>登入</h1>
        <span class="user">
          <input type="text" name="username_log" class="input" v-model="input_login.username" :placeholder="lg_guide.user" />
        </span>
        <span class="pass signup_pass">
          <input type="password" name="username_pwd" class="input" v-model="input_login.password" :placeholder="lg_guide.pwd" />
        </span>
        <div class="button">
          <button class="btn_log google" @click="googlelogin('login')" v-if="isgooglelogin">Login <br class="mb_br" /> with  Google</button>
          <button disabled class="btn_log btn_gray" v-else-if="isgooglelogin=== false">登入中<i class="fas fa-spinner"></i></button>
          <button class="btn_log" @click="login" v-if="islogin">登入</button>
          <button disabled class="btn_log btn_gray" v-else-if="islogin=== false">登入中<i class="fas fa-spinner"></i></button>
        </div>
      </div>

      <div class="card sign" v-show="pop_card === 'signup'">
        <a @click="close">
          <i class="far fa-times-circle"></i>
        </a>
        <h1>註冊</h1>
        <span class="user signup_user" :class="{error: usernamehasaccount}">
          <input type="text" name="signup_username" class="input" v-model="input_signup.username" :placeholder="su_guide.user" @keyup="checkuser(input_signup.username)" @click="clearerror" />
        </span>
        <span class="pass">
          <input type="password" name="signup_pass" class="input" v-model="input_signup.password" :placeholder="su_guide.pwd" @click="clearerror" />
        </span>
        <span class="repass">
          <input type="password" name="signup_repass" class="input" v-model="input_signup.repassword" :placeholder="su_guide.repwd" @click="clearerror" />
        </span>
        <div class="button">
          <button class="btn_log google" @click="googlelogin('signup')" v-if="isgooglesignup">Sign up <br class="mb_br" /> with Google</button>
          <button disabled class="btn_log btn_gray" v-else-if="isgooglesignup === false">註冊中<i class="fas fa-spinner"></i></button>
          <button class="btn_log" @click="signup" v-if="issignup">送出</button>
          <button disabled class="btn_log btn_gray" v-else-if="issignup === false">註冊中<i class="fas fa-spinner"></i></button>
        </div>
    </div>

    </div>
  </div>
</template>

<script>
export const API_URL = process.env.VUE_APP_API_URL;
var CryptoJS = require("crypto-js")

export default {
  name: 'myheader',
  props:['googleAPI',"haslogin" ,"username"],
  data () {
    return {
      usernamehasaccount: false,
      pop: false,
      pop_card: null,
      lg_guide: {user: '', pwd: ''},
      su_guide: {user: "請輸入英文和數字，至少5個字元", pwd: "請輸入至少8個字元，包含大小寫" ,repwd: ''},
      input_login : {username: '', password: '',hide: ''},
      input_signup : {id:null,username:'',password:'',repassword:'',hide: ''},
      isgooglelogin: true,
      islogin: true,
      isgooglesignup : true,
      issignup: true,
    }
  },
  methods: {
    open ($type) {
      this.pop = true;
      this.pop_card = $type;
    },
    close () {
      this.pop = false;
    },
    login () {
      var vm = this;
      vm.islogin = false;
      document.querySelector('.signup_pass').classList.remove('error');
      vm.input_login.hide = CryptoJS.MD5(vm.input_login.password).toString();
      vm.$axios.post(API_URL+'/api/login', this.input_login)
      .then( (res)=> {
        console.log(res.data);
        if (res.data != false) {
          window.location.reload()
        }else{
          document.querySelector('.signup_pass').classList.add('error');
          vm.islogin = true;
        }
      })
      .catch( (res)=> {
        console.log(res);
      })
    },
    googlelogin (val) {
      var vm = this;
      var CLIENT_ID = vm.googleAPI.CLIENT_ID;
      var API_KEY = vm.googleAPI.API_KEY;

      var gapi = window.gapi;
      
      var authParams = {
        'response_type':'token',
        'client_id':CLIENT_ID,
        'immediate':false,
        'scope': ['https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'],
      };

      //init
      gapi.load("client:auth2",function() {
        gapi.auth2.init({client_id: CLIENT_ID});
      });

      gapi.auth.authorize(authParams, myCallback);
          //登入結果,設定token
      function myCallback(authResult){
        if (authResult && authResult['access_token']) {
          gapi.auth.setToken(authResult);
            console.log(authResult)
            if (val === 'login') {
              vm.isgooglelogin = false;
            }else{
              vm.isgooglesignup = false;
            }
            loadClient();
        }else{
          // Authorization failed or user declined
        }
      }

      //連API
      function loadClient() {
        gapi.client.setApiKey(API_KEY);
        return gapi.client.load("https://www.googleapis.com/discovery/v1/apis/people/v1/rest")
        .then( function () { 
          console.log("GAPI client loaded for API"); 
          getuseremail();
        },
        function(err) { console.error("Error loading GAPI client for API", err); });
      }

      function getuseremail () {
        gapi.client.people.people.get({
            'resourceName': 'people/me',
            //通常你會想要知道的用戶個資↓
            'personFields': 'names,phoneNumbers,emailAddresses,addresses,residences,genders,birthdays,occupations',
        }).then(function (res) {
            let email = {username: res.result.emailAddresses[0].value};
            vm.$axios.post(API_URL+'/api/googlelogin', email)
            .then( (res)=> {
              console.log(res);
              if (res.data === 1 ){
                if (vm.isgooglesignup === false && vm.$parent.events.length !== 0) {
                  vm.$parent.eventsToBackend();
                }
                window.setTimeout(window.location.reload(), 1000);
              }
            })
            .catch( (res)=> {
              console.log(res);
              alert('登入錯誤 !');
            })
        });

      }
    },
    checkuser(val) {
      var vm = this;
      vm.usernamehasaccount = false;
      document.querySelector('.signup_user').classList.remove('error');
      vm.$axios.post(API_URL+'/api/checkuser', {username: val})
      .then( (res)=> {
        if (res.data.length === 1) {
          vm.usernamehasaccount = true;
        }
      })
      .catch( (res)=> {
        console.log(res);
      })
    },
    signup() {
      var vm = this;
      function usercheck () {
        if (/^(?=.*[a-z])(?=.*[0-9])[a-zA-Z0-9]{5,}$/.test(vm.input_signup.username)) {
          console.log('帳號正確')
          return true
        }else{
          document.querySelector('input[name="signup_username"]').classList.add('error');
          vm.input_signup.username = '';
        }
      }
      function pwdcheck () {
        if (/^(?=.*[a-z])(?=.*[A-Z])[a-zA-Z0-9]{8,}$/.test(vm.input_signup.password)) {
          console.log('密碼正確')
          return true
        }else{
          document.querySelector('input[name="signup_pass"]').classList.add('error');
          vm.input_signup.password = '';
        }
      }
      function repwdcheck () {
        if (vm.input_signup.password === vm.input_signup.repassword && vm.input_signup.repassword !== '') {
          console.log('確認密碼正確')
          return true
        }else{
          document.querySelector('input[name="signup_repass"]').classList.add('error');
          vm.su_guide.repwd = '確認密碼錯誤';
          vm.input_signup.repassword = '';
        }
      }
      if (usercheck () === true & pwdcheck () === true & repwdcheck () === true && vm.usernamehasaccount === false) {
        vm.issignup = false;
        console.log('sucsess');
        vm.input_signup.hide = CryptoJS.MD5(vm.input_signup.password).toString();
        vm.$axios.post(API_URL+'/api/signup', vm.input_signup)
        .then( (res)=> {
          console.log(res);
          if (vm.$parent.events.length !== 0) {
            this.$parent.eventsToBackend();
          }
          window.setTimeout(window.location.reload(), 1000);
        })
        .catch( (res)=> {
          console.log(res);
        })
      }
    },
    clearerror (e) {
      e.target.classList.remove('error');
      if (e.target === document.querySelector('input[name="signup_repass"]')) {
          this.su_guide.repwd = '';
      }
    },
    logout () {
      this.$axios.delete(API_URL+'/api/session')
      .then( (res)=> {
        console.log(res);
        window.location.reload()
      })
      .catch( (res)=> {
        console.log(res);
      })
    }
  },
  mounted () {

    this.$bus.$on('openlogin', (val) => {
      this.pop = val.pop;
      this.pop_card = val.pop_card;
    })

    this.$bus.$on('opensignup', (val) => {
      this.pop = val.pop;
      this.pop_card = val.pop_card;
    })

  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">

  @import '../scss/base/var';
  @import '../scss/mixin/mixin';

  header {
    display: flex;
    justify-content: center;
    .title {
      width: 100%;
      display: flex;
      justify-content: flex-start;
      align-items: center;
      margin-left: 1%;
      h1 {
        display: inline-block;
      }
      .firstwords {
        color: $red;
      }
      .secwords {
        color: green;
      }
      .thirdwords {
        color: $blue;
      }
    }
    nav {
      width: 70%;
      display: flex;
      justify-content: flex-end;
      margin-right: 5%;

      .user {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-right: 10%;
        h2 {
          display: flex;
          justify-content: center;
          align-items: center;
          background-color: $blue;
          color: white;
          border-radius: 10px;
          padding: 0% 15%;
          white-space: nowrap;
        }
      }

      .memberbtn {
        margin: 0 2%;

        h2 {
          background-color: white;
          border: 1px solid #b1b1b1;
          border-radius: 20px;
          padding: 10% 0;
          cursor: pointer;
          user-select:none;
          width: 120px;
          text-align: center;
          color: #383838;

          &:hover {
            color: $blue;
            background-color: #f6fafe;
          }
        }
      }
    }
  }

  @include pop(member);
  .member_pop {
    .card {
      @include card;
      width: 90%;

      .fa-times-circle {
        position: absolute;
        top: 0;
        right: 0;
        font-size: 60px;
        color: $blue;
        cursor: pointer;

        &:hover{
          transition: color .2s;
          color: $red;
        }
      }

      span {
        position: relative;
        display: flex;
        justify-content: center;
        margin: 25px;

        .input {
          width: 100%;
          font-size: 30px;
          margin: 5% auto;

          &::-webkit-input-placeholder { /* Chrome/Opera/Safari */
            font-size: 20px;
            text-align: center;
            color: #383838;
          }
        }
        .error {
          border:1px solid red;
          animation-name: error;
          animation-timing-function: linear;
          animation-duration: .5s;
          @keyframes error {
            0% {
              transform: translateX(0);
            }
            25% {
              transform: translateX(-15px);
            }
            50% {
              transform: translateX(0);
            }
            75% {
              transform: translateX(15px);
            }
            100% {
              transform: translateX(0);
            }
          }

          &::-webkit-input-placeholder{
            color: red;
          }
        }
        @mixin pseudo($name) {
          position: absolute;
          top: 0;
          left: 0;
          content: $name;
          color: #383838;
          font-size: 30px;
          transform: translate(0%, -100%);
          white-space: nowrap;
        }

        &.user::before {
          @include pseudo ('帳號');
        }

        &.pass::before {
          @include pseudo ('密碼');
        }

        &.repass::before {
          @include pseudo ('重新輸入密碼');
        }
      }

      span.error {
        &.signup_pass::after{
          position: absolute;
          top: 100%;
          left: 50%;
          content: '帳號或密碼錯誤';
          color: red;
          font-size: 20px;
          transform: translate(-50%, 0%);
          white-space: nowrap;
        }
        &.signup_user::after {
          position: absolute;
          top: 50%;
          right: 0%;
          content: '此帳號有人使用';
          color: red;
          font-size: 25px;
          transform: translate(110%, -50%);
          white-space: nowrap;
        }
      }

      .button {
        align-items: center;
        margin: 3%;
          button {
            width: 35%;
            font-size: 20px;
          }
         .google {
           background-color: #D6492F;
           justify-content: flex-end;
           position:relative;

           .mb_br {
             display: none;
           }

           &::before {
              content: '';
              background-image: url('../assets/google.png');
              width: 50px;
              height: 50px;
              position: absolute;
              top: 50%;
              left: 0;
              background-color: white;
              background-repeat: no-repeat;
              background-position: center;
              background-size: cover;
              transform: translate(0, -50%);
              border-radius: 100%;
            }
         }
         .btn_gray {
           background-color: gray;
           @include loading;
         }
      }
    }
  }
  @include rwd (large) {
    header {
      .title {
        h1 {
          font-size: 40px;
        }
      }
      nav .user h2 {
        font-size: 30px;
        padding: 0% 2%;
      }
    }    
    .member_pop .card {
      padding: 0;
      height: 95vh;
      span {
        margin: 20px;
      }
    }

  }
  @include rwd (medium) {
    header {
      .title {
        h1 {
          font-size: 30px;
        }
      }
      nav .user {
        display: none;
      }
    }
    .member_pop .card span.error.signup_user::after {
      top: 100%;
      right: 0%;
      font-size: 20px;
      transform: translate(0%, 0%);
    }
    .member_pop .card .button {
      
      button {
        width : 40%;
      }
      .google{
        &::before {
          width: 40px;
          height: 40px;
          left : 3%;
        }
      }
    }
  }
  @include rwd (small) {
    header {
      nav .memberbtn h2 {
        width: 110px;
      }
      .title {
        h1 {
          line-height: 5px;
        }
      }
    }
    .member_pop .card {
      overflow-y: auto;
      .button { 
        button {
          height : 75px;
        }
        .google {
          text-align: left;
          padding-left: 10%;
          &::before {
            width: 30px;
            height: 30px;
            left: 4%;
          }
          .mb_br {
            display: block;
          }
        }
      }
    } 
  }
  @include rwd (xsmall) {
    header {
      .title {
        h1 {
          font-size: 25px;
        }
      }
      nav .memberbtn h2 {
        width: 60px;
        font-size: 25px;
      }
    }
    .member_pop .card {
      width: 80%;
      .button .google {
        padding-left: 5%;
        &::before {
          display: none;
        }
      }
      span .input::-webkit-input-placeholder {
        font-size: 12px;
      }
    }
  }
</style>
