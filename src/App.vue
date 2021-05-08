<template>
  <myheader :googleAPI="googleAPI" :haslogin="haslogin" :username="username" />
  <main>
    <calendar @event="updateevent" :haslogin="haslogin" />
    <editbar @updateguide="getguidenum" @updateedit="closeedit" :haslogin="haslogin" :guide="guide" :mobileedit="mobileedit" />
  </main>
  <div class="space" :class="{spaceon: mobileedit}"></div>
  <div class="btn">
    <button id="mobileshiftbtn" class="button mbtn" @click="manage"><h3>管理班表</h3></button>
    <button disabled class="button buttonupload" v-if="upload === true"><h3 class="uploading">上傳中...</h3><i class="fas fa-spinner"></i></button>
    <button id="authButton" class="button" v-else-if="upload === false"><h3>新增到Google行事曆</h3></button>
    <button id="addButton" class="button" @click="execute" v-show="upload === 3"><h3>新增到Google行事曆</h3></button>
  </div>
  <div class="sucess_pop" id="sucess_pop">
    <div class="sucess_pop_in">
      <div class="card">
        <h1>上傳完成 !</h1>
        <h2 v-show="haslogin === false">登入帳號，可以紀錄班表跟班別哦 !</h2>
        <div class="button">
          <button class="btn_sign" v-show="haslogin === false" @click="opensignup">還沒有帳號，先註冊</button>
          <button class="btn_log" v-show="haslogin === false" @click="openlogin">登入</button>
          <button class="btn_log" v-show="haslogin" @click="close">關閉視窗</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import myheader from './components/header.vue'
import calendar from './components/calendar.vue'
import editbar from './components/editbar.vue'

export const API_URL = process.env.VUE_APP_API_URL;

export default {
  name: 'App',
  data () {
    return {
      haslogin: '',
      username: null,
      guide : 1,
      events : [],
      days_count : '',
      upload: false,
      googlelogin: false,
      mobileedit: false,
      googleAPI:{
        CLIENT_ID: '280793763874-hm3kggc61gqs775vo6sklk8mihqoj28b.apps.googleusercontent.com',
        API_KEY:
        'AIzaSyAR30nDKLsSwbZKBXyi4QSCr2PxplRtmug'
      },
      authParams: {
        'response_type':'token',
        'client_id':'280793763874-hm3kggc61gqs775vo6sklk8mihqoj28b.apps.googleusercontent.com',
        'immediate':false,
        'scope': ["https://www.googleapis.com/auth/calendar", "https://www.googleapis.com/auth/calendar.events", "https://www.googleapis.com/auth/userinfo.email" ]
      },
    }
  },  
  components: {
    myheader,
    calendar,
    editbar,
  },
  methods: {
    closeedit (val) {
      this.mobileedit = val;
    },
    manage () {
      this.mobileedit = true;
    },
    getguidenum(val) {
      this.guide = val;
      this.$bus.$emit('updateguide',val);
    },
    updateevent (val) {
      this.events = [];
      const year = val[0];
      let month;
      if (val[1].toString().length == 1) {
        month = '0' + val[1];
      }else{
        month = val[1];
      }
      this.days_count = val[2];
      let event = [];
      let shifts = val[3];
      for (let j = 0; j <= shifts.length -1 ; j++) {
        for (let k = 0 ; k <= shifts[j].length - 1; k++) {
          if (shifts[j][k].day != "") {
            let daystr;
            if (shifts[j][k].day.toString().length == 1) {
              daystr = '0' + shifts[j][k].day;
            }else{
              daystr = shifts[j][k].day;
            }
            let starttime = shifts[j][k].starttime;
            let endtime = shifts[j][k].endtime;
            let enday;
            let enddaystr;
            if (starttime > endtime) {
              enday = shifts[j][k].day + 1;

            }else{
              enday = shifts[j][k].day;
            }
            if (enday.toString().length == 1) {
              enddaystr = '0' + enday;
            }else{
              enddaystr = enday;
            }
            let obj = {
              'summary': shifts[j][k].name, 
              'start': {
              'dateTime': `${year}-${month}-${daystr}T${starttime}:00+08:00`,
              },
              'end': {
                'dateTime': `${year}-${month}-${enddaystr}T${endtime}:00+08:00`,
              }
            }
            event.push(obj);
          }
        }
      }
      this.events = event;
    },
    execute () {
      if (this.events.length != 0) {
        document.getElementById('addButton').style.display = "none";
        this.upload = true;
        var gapi = window.gapi;
        //多筆事件
        const batch = gapi.client.newBatch();
        this.events.map((r, j) => {
          batch.add(gapi.client.calendar.events.insert({
          'calendarId': 'primary',
          'resource': this.events[j]
          }))
        })

        batch.then( (res)=> {
          console.log(res);
          document.getElementById('sucess_pop').classList.add('on');
          console.log('all jobs now dynamically done!!!')
          this.upload = 3;
        });
      }else{
        alert('行事曆沒有資料!')
      } 
    },
    eventsToBackend() {
      let year = this.events[0].start.dateTime.split("-")[0];
      let month = this.events[0].start.dateTime.split("-")[1];

      let data = {year: year, month: month , events : JSON.stringify(this.events)};

      this.$axios.get(API_URL+'/api/events', { params: data })
        .then( (res)=> {
          console.log(res);
        })
        .catch( (res)=> {
          console.log(res);
        })
    },
    getCookies () {
      var vm = this;
      const token = this.$Cookies.get('api_token');
      if (token) {
        vm.haslogin = true;
        vm.username = this.$Cookies.get('user');
      }else{
        vm.haslogin = false;
      }
    },
    close () {
      document.getElementById('sucess_pop').classList.remove('on');
    },
    opensignup () {
      let obj = {pop: true,pop_card: "signup"}
      this.$bus.$emit('opensignup', obj);
      document.getElementById('sucess_pop').classList.remove('on');
    },
    openlogin () {
      let obj = {pop: true,pop_card: "login"}
      this.$bus.$emit('openlogin', obj);
      document.getElementById('sucess_pop').classList.remove('on');
    },
  },
  watch : {
    events: function () {
      if (this.haslogin === true) {
        this.eventsToBackend();
      }
    }
  },
  created () {
    this.getCookies();
  },
  mounted() {

      var CLIENT_ID = this.googleAPI.CLIENT_ID;
      var API_KEY = this.googleAPI.API_KEY;

      var gapi = window.gapi;

      //init
      gapi.load("client:auth2",()=> {
        gapi.auth2.init({client_id: CLIENT_ID});
      });

    //登入btn
		document.getElementById('authButton').onclick = ()=> {
      gapi.auth.authorize(this.authParams, myCallback)
    };
    
    //登入結果,設定token
		function myCallback(authResult){
      if (authResult && authResult['access_token']) {
        gapi.auth.setToken(authResult);
          loadClient();
      }else{
        // Authorization failed or user declined
      }
		}

		//連API
		function loadClient() {
      gapi.client.setApiKey(API_KEY);
      return gapi.client.load("https://content.googleapis.com/discovery/v1/apis/calendar/v3/rest")
      .then( function () { 
        console.log("GAPI client loaded for API"); 
        document.getElementById('addButton').click();
      },
      function(err) { console.error("Error loading GAPI client for API", err); });
    }
    
  },
}
</script>

<style lang="scss">
@import url('https://fonts.googleapis.com/css2?family=Noto+Sans&display=swap'); //思源黑體
@import './scss/base/var';
@import './scss/mixin/mixin';

body{
    background-color: #fffeea;
}

#app {
  font-family: 'Noto Sans', sans-serif;
}
h1 {
  font-size: 50px;
}
h2 {
  font-size: 40px;
}
h3 {
  font-size: 30px;
}
h4 {
  font-size: 25px;
}
p {
  font-size: 16px;
}
main {
  display: flex;
}
.space {
  height: 0;
  transition: height 1s ease;
}
.btn {
  margin-top: 25px;
  display: flex;
  justify-content: space-around;

  .button {
    display: flex;
    align-items: center;
    @include btn ($blue,$green);
    color: white;
    padding: 0 7%;
  }
  .mbtn {
    display: none;
  }
  .buttonupload {
    display: flex;
    align-items: center;
    color: white;
    padding: 0 7%;
    @include btn (gray,gray);
    .uploading {
      padding-right: 15px;
    }
    @include loading;
  }
}
@include pop (sucess);
.card {
  @include card;

  h1 {
    color: $blue;
  }
  .button {
    width: 100%;
    display: flex;
    justify-content: space-around;

    button {
      font-size: 30px;
      color: white;
      padding: 2%;
      width: 35%;
    }

    .btn_sign {
      @include btn ($red,$green);

    }
    .btn_log {
      @include btn ($blue,$green);
    }
  }
}
@include rwd (medium) {
  main {
    flex-direction: column;
  }
  .spaceon {
    height: 60px;
  }
  .btn {
    .mbtn {
      display: flex;
    }
  }
  .sucess_pop .sucess_pop_in .card{
    width: 80%;
    .button {
      flex-direction: column;
      align-items: center;
      button {
        margin: 4% auto;
        width: 80%;
      }
    }
  }
}
@include rwd (small) {
  .btn {
    flex-wrap: wrap;
    .button {
      width: 100%;
      justify-content: center;
      margin: 2%;
    }
  }
}
</style>
