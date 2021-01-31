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
          <button class="btn_log" @click="login">登入</button>
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
          <button class="btn_log" @click="signup">送出</button>
        </div>
    </div>

    </div>
  </div>
</template>

<script>
import axios from 'axios'
var CryptoJS = require("crypto-js")

export default {
  name: 'myheader',
  data () {
    return {
      haslogin: '',
      username: null,
      usernamehasaccount: false,
      pop: false,
      pop_card: null,
      lg_guide: {user: '', pwd: ''},
      su_guide: {user: "請輸入英文和數字，至少5個字元", pwd: "請輸入至少8個字元，包含大小寫" ,repwd: ''},
      input_login : {username: '', password: '',hide: ''},
      input_signup : {id:null,username:'',password:'',repassword:'',hide: ''}
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
      document.querySelector('.signup_pass').classList.remove('error');
      vm.input_login.hide = CryptoJS.MD5(vm.input_login.password).toString();
      axios.post('/api/login', this.input_login)
      .then( (res)=> {
        console.log(res.data);
        if (res.data.length === 1) {
          window.location.reload()
        }else{
          document.querySelector('.signup_pass').classList.add('error');
        }
      })
      .catch( (res)=> {
        console.log(res);
      })
    },
    checkuser(val) {
      var vm = this;
      vm.usernamehasaccount = false;
      document.querySelector('.signup_user').classList.remove('error');
      axios.post('/api/checkuser', {username: val})
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
        console.log('sucsess');
        vm.input_signup.hide = CryptoJS.MD5(vm.input_signup.password).toString();
        axios.post('/api/signup', vm.input_signup)
        .then( (res)=> {
          console.log(res);
          window.location.reload();
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
    getsession () {
      var vm = this;
      axios.get('/api/session')
      .then( (res)=> {
        if (res.data.id !== null) {
          vm.haslogin = true;
          vm.username = res.data.user;
        }else{
          vm.haslogin = false;
        }
      })
      .catch( (res)=> {
        console.log(res);
      })
    },
    logout () {
      axios.delete('/api/session')
      .then( (res)=> {
        console.log(res);
        window.location.reload()
      })
      .catch( (res)=> {
        console.log(res);
      })
    }
  },
  created() {
    this.getsession();
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
        margin: 3%;
      }
    }
  }
  @include rwd (large) {
    .member_pop .card {
      width: 75%;
    }
  }
  @include rwd (medium) {
    header {
      .title {
        h1 {
          font-size: 30px;
        }
      }
    }
    .member_pop .card span.error.signup_user::after {
      top: 100%;
      right: 0%;
      font-size: 20px;
      transform: translate(0%, 0%);
    }
  }
  @include rwd (small) {
    header {
      .title {
        h1 {
          line-height: 5px;
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
      span .input::-webkit-input-placeholder {
        font-size: 12px;
      }
    }
  }
</style>
