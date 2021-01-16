<template>
  <myheader/>
  <main>
    <calendar @event="updateevent" />
    <editbar :guide="guide" @updateguide="getguidenum" />
  </main>
  <div class="btn">
    <button disabled class="button buttonupload" v-if="upload"><h3 class="uploading">上傳中...</h3><i class="fas fa-spinner"></i></button>
    <button id="authButton" class="button" v-else-if="upload === false"><h3>新增到Google行事曆</h3></button>
    <button id="addButton" style="display:none" @click="execute"><h3>新增事件</h3></button>
  </div>
  <div class="sucess_pop" id="sucess_pop">
    <div class="sucess_pop_in">
      <div class="card">
        <h1>上傳完成 !</h1>
        <h2>登入帳號，可以紀錄班表跟班別哦 !</h2>
        <div class="button">
          <button class="btn_sign">還沒有帳號，先註冊</button>
          <button class="btn_log">登入</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import myheader from './components/header.vue'
import calendar from './components/calendar.vue'
import editbar from './components/editbar.vue'


export default {
  name: 'App',
  data () {
    return {
      guide : 1,
      events : [],
      days_count : '',
      upload: false,
    }
  },
  components: {
    myheader,
    calendar,
    editbar
  },
  methods: {
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
              if (enday.toString().length == 1) {
                enddaystr = '0' + enday;
              }else{
                enddaystr = enday;
              }
            }else{
              enddaystr = shifts[j][k].day;
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

        batch.then( ()=> {
          this.upload = false;
          document.getElementById('sucess_pop').classList.add('on');
          console.log('all jobs now dynamically done!!!')
        });
      }else{
        alert('行事曆沒有資料!')
      } 
    }
  },
  mounted() {
      var CLIENT_ID = '280793763874-immfv9rpbb9l81g28c4paa1dfa912fs0.apps.googleusercontent.com';
      var API_KEY = 'AIzaSyAR30nDKLsSwbZKBXyi4QSCr2PxplRtmug';

      var gapi = window.gapi;
      
      var authParams = {
        'response_type':'token',
        'client_id':CLIENT_ID,
        'immediate':false,
        'scope':['https://www.googleapis.com/auth/calendar https://www.googleapis.com/auth/calendar.events']
      };

      //init
      gapi.load("client:auth2",function() {
        gapi.auth2.init({client_id: CLIENT_ID});
      });

    //登入btn
		document.getElementById('authButton').onclick = function(){
      gapi.auth.authorize(authParams, myCallback)
    };
    
    //登入結果,設定token
		function myCallback(authResult){
      if (authResult && authResult['access_token']) {
        gapi.auth.setToken(authResult);
          console.log(authResult)
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
  .buttonupload {
    display: flex;
    align-items: center;
    color: white;
    padding: 0 7%;
    @include btn (gray,gray);
    .uploading {
      padding-right: 15px;
    }
    svg {
      animation-name: loading;
      animation-duration: 1s;
      animation-iteration-count: infinite;
      font-size: 30px;

      @keyframes loading {
        0% {
          transform: rotate(0);
        }
        100% {
          transform: rotate(360deg);
        }
      }
    }

  }
}
@include pop (sucess);
.card {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  width: 50%;
  background-color: white;
  border: 3px solid gray;
  border-radius: 25px;
  padding: 5%;
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
</style>
