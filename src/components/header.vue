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
      <div class="sign memberbtn" @click="open('signup')">
        <h2>註冊</h2>
      </div>
      <div class="log memberbtn" @click="open('login')">
        <h2>登入</h2>
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
          <input type="text" name="username_log" class="input" />
        </span>
        <span class="pass">
          <input type="password" name="username_log" class="input" />
        </span>
        <div class="button">
          <button class="btn_log">登入</button>
        </div>
      </div>

      <div class="card sign" v-show="pop_card === 'signup'">
        <a @click="close">
          <i class="far fa-times-circle"></i>
        </a>
        <h1>註冊</h1>
        <span class="user">
          <input type="text" name="username_pass" class="input" />
        </span>
        <span class="pass">
          <input type="password" name="username_pass" class="input" />
        </span>
        <span class="repass">
          <input type="password" name="username_repass" class="input" />
        </span>
        <div class="button">
          <button class="btn_log">送出</button>
        </div>
    </div>

    </div>
  </div>
</template>

<script>
export default {
  name: 'myheader',
  data () {
    return {
      pop: false,
      pop_card: null,
    }
  },
  methods: {
    open ($type) {
      this.pop = true;
      this.pop_card = $type;
    },
    close () {
      this.pop = false;
    }
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

      .button {
        margin: 3%;
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
    }
  }
</style>
