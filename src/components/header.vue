<template>
  <header>
    <div class="title">
      <h1 class="firstwords">
        Happy&nbsp;
      </h1>
      <h1 class="secwords">
        Shifts&nbsp;
      </h1>
      <h1 class="thirdwords">
        班表管理
      </h1>
    </div>
    <nav>
      <div class="sign memberbtn">
        <h2>註冊</h2>
      </div>
      <div class="log memberbtn">
        <h2>登入</h2>
      </div>
    </nav>
  </header>
</template>

<script>
export default {
  name: 'myheader',
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
</style>
