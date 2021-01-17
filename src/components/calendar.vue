<template>
  <div class="calendar">
    <table>
      <thead>
        <tr class="monthrow">
          <td class="changeM" @click="prevMonth">
            <i class="fas fa-arrow-left"></i>
          </td>
          <td class="monthyear">{{year}}年{{month}}月</td>
          <td class="changeM" @click="nextMonth()">
            <i class="fas fa-arrow-right"></i>
          </td>
        </tr>
        <tr class="weeksrow">
          <td class="weeks">星期日</td>
          <td class="weeks">星期一</td>
          <td class="weeks">星期二</td>
          <td class="weeks">星期三</td>
          <td class="weeks">星期四</td>
          <td class="weeks">星期五</td>
          <td class="weeks">星期六</td>
        </tr>
      </thead>
      <tbody>
        <cdays v-for="(day,index) in days" :key="index" :day="day" :shifts="shifts[index]" @update="updateindex"/>
          <div class="guide guide2" v-show="guide === 2">
            <span class="arrow">🡅</span>
            可以點擊選擇指定的日期哦 !
            <button class="btn" @click="iknow"><p>知道了</p></button>
          </div>
      </tbody>
    </table>
  </div>
</template>

<script>
import cdays from './cdays.vue'

export default {
  data () {
    return {
      year : '',
      month : '',
      days_count: '',
      days : [[],[],[],[],[],[]],
      shifts : [[],[],[],[],[],[]],
      nowindex: '',
      guide : 0,
    }
  },
  components: {
    cdays
  },
  methods :{
    updateguide(val) {
      this.guide = val;
    },
    iknow () {
      document.querySelector('.guide2').style.display="none";
      this.$bus.$emit('updateguide',0);
    },
    updateindex(val) {
      var vm = this;
      vm.nowindex = val;
    },
    prevMonth () {
      var vm = this;
      vm.month -= 1;
      if (vm.month === 0) {
        vm.month = 12;
        vm.year -= 1;
      }
      vm.updatecar ();
    },
    nextMonth () {
      var vm = this;
      vm.month += 1;
      if (vm.month === 13) {
        vm.month = 1;
        vm.year += 1;
      }
      vm.updatecar ();
    },
    updatecar () {
      var vm = this;
      vm.days = [[],[],[],[],[],[]];
      vm.shifts = [[],[],[],[],[],[]],
      vm.days_count = new Date(vm.year, vm.month, 0).getDate();
      // push calendar day
      let firstday = new Date(vm.year, vm.month -1, 1, 0).toString();
      var dayofweek = firstday.slice(0 , 3);

      // array for loop function
      function Dayprocess () {
        if (dayofweek === 'Sat') {
          var firstnum = 1;
        }else{
          for (let i = 1; i <= 7 ; i++) {
            vm.days[0].push(i);    
            if (vm.days[0].length == 7) {
              break;
            }                
          }
          var lastnum = vm.days[0][6] + 1;
        }
        if (firstnum) {
          for (let i = 1; i <= 7 ; i++) {
            vm.days[1].push(i);
          }
        }else{
          for (let i = 1; i <= 7 ; i++) {
            vm.days[1].push(lastnum);
            lastnum++
          }
        }
        let lastnum2 = vm.days[1][6] + 1;
        for (let i = 1; i <= 7 ; i++) {
          vm.days[2].push(lastnum2);
          lastnum2++
        }
        let lastnum3 = vm.days[2][6] + 1;
        for (let i = 1; i <= 7 ; i++) {
          vm.days[3].push(lastnum3);
          lastnum3++
        }
        let lastnum4 = vm.days[3][6] + 1;
        for (let i = 1; i <= 7 ; i++) {
          if (lastnum4 <= vm.days_count) {
            vm.days[4].push(lastnum4);
            lastnum4++
          }else{
            vm.days[4].push('');
          }
        }
        let lastnum5 = vm.days[4][6] + 1;
        if (lastnum5 == 1) {
          lastnum5 = ''
        }
        for (let i = 1; i <= 7 ; i++) {
          if (lastnum5 === '') {
            vm.days[5].push(lastnum5);
          }else{
            if (lastnum5 <= vm.days_count) {
              vm.days[5].push(lastnum5);
              lastnum5++
            }else{
              vm.days[5].push('');
            }
          }
        }
      }

      if (dayofweek == 'Sun') {
        Dayprocess ();
      }else if (dayofweek === 'Mon') {
        vm.days[0].push('');
        Dayprocess ();
      }else if (dayofweek === 'Tue') {
        vm.days[0].push('','');
        Dayprocess ();
      }else if (dayofweek === 'Wed') {
        vm.days[0].push('','','');
        Dayprocess ();
      }else if (dayofweek === 'Thu') {
          vm.days[0].push('','','','');
          Dayprocess ();
      }else if (dayofweek === 'Fri') {
          vm.days[0].push('','','','','');
          Dayprocess ();
      }else if (dayofweek === 'Sat') {
        for (let i = 1; i <= 7 ; i++) {
          vm.days[0].push('','','','','','');
        }        
        Dayprocess ();
      }

        // make shift arr
      let obj = {name: '', starttime : '', endtime : '' ,day: ''};
      for (let i = 0 ; i <= vm.days.length -1; i++) {
        for(let j = 1 ; j <= vm.days[i].length; j++) {
          vm.shifts[i].push(obj);
        }
      }
    },
    checkrow () {
      var vm = this;
      function row(num) {
        if (vm.days[num][0] === '' && vm.days[num][6] === '') {
        vm.days.splice(num , 1);
        vm.shifts.splice(num , 1);
        }
      }
      row(0);
      row(vm.days.length -1);
    },
    tocar(obj) {
      var vm = this;
      let day;
      if (vm.nowindex <= 6) {
        vm.shifts[0][vm.nowindex] = obj;
        day = vm.days[0][vm.nowindex % 7];
        vm.shifts[0][vm.nowindex].day = day;
      }else if(vm.nowindex <= 13) {
        vm.shifts[1][vm.nowindex % 7] = obj;
        day = vm.days[1][vm.nowindex % 7];
        vm.shifts[1][vm.nowindex % 7].day = day;
      }else if(vm.nowindex <= 20) {
        vm.shifts[2][vm.nowindex % 7] = obj;
        day = vm.days[2][vm.nowindex % 7];
        vm.shifts[2][vm.nowindex % 7].day = day;
      }else if(vm.nowindex <= 27) {
        vm.shifts[3][vm.nowindex % 7] = obj;
        day = vm.days[3][vm.nowindex % 7];
        vm.shifts[3][vm.nowindex % 7].day = day;
      }else if(vm.nowindex <= 34) {
        vm.shifts[4][vm.nowindex % 7] = obj;
        day = vm.days[4][vm.nowindex % 7];
        vm.shifts[4][vm.nowindex % 7].day = day;
      }else if(vm.nowindex <= 41) {
        vm.shifts[5][vm.nowindex % 7] = obj;
        day = vm.days[5][vm.nowindex % 7];
        vm.shifts[5][vm.nowindex % 7].day = day;
      }
      this.$bus.$emit('nextcar',day);
      this.$emit('event', [this.year,this.month,this.days_count,this.shifts]); 
    }
  },
  watch : {
    shifts : function () {
      this.checkrow();
    },
  },
  created() {
    var vm = this;
    (function () {
        let date = new Date()
        vm.year = date.getFullYear();
        let month = date.getMonth();
        vm.month = month + 1;
        // count day
        vm.days_count = new Date(vm.year, month, 0).getDate();
    }())
  },
  mounted() {
    var vm = this;
    (function () {
      // push calendar day
      let date = new Date; // get current date
      let firstday = new Date(date.setDate(1)).toUTCString();
      let dayofweek = firstday.split(",")[0];

      // array for loop function
      function Dayprocess () {
        for (let i = 1; i <= 7 ; i++) {
            vm.days[0].push(i);    
            if (vm.days[0].length == 7) {
              break;
            }                
        } 
        let lastnum = vm.days[0][6] + 1;
        for (let i = 1; i <= 7 ; i++) {
          vm.days[1].push(lastnum);
          lastnum++
        }
        let lastnum2 = vm.days[1][6] + 1;
        for (let i = 1; i <= 7 ; i++) {
          vm.days[2].push(lastnum2);
          lastnum2++
        }
        let lastnum3 = vm.days[2][6] + 1;
        for (let i = 1; i <= 7 ; i++) {
          vm.days[3].push(lastnum3);
          lastnum3++
        }
        let lastnum4 = vm.days[3][6] + 1;
        for (let i = 1; i <= 7 ; i++) {
          vm.days[4].push(lastnum4);
          lastnum4++
        }
        let lastnum5 = vm.days[4][6] + 1;
        for (let i = 1; i <= 7 ; i++) {
          if (lastnum5 <= vm.days_count) {
            vm.days[5].push(lastnum5);
            lastnum5++
          }else{
            vm.days[5].push('');
          }
        }
      }

      if (dayofweek == 'Sun') {
        Dayprocess ();
      }else if (dayofweek === 'Mon') {
        vm.days[0].push('');
        Dayprocess ();
      }else if (dayofweek === 'Tue') {
        vm.days[0].push('','');
        Dayprocess ();
      }else if (dayofweek === 'Wed') {
        vm.days[0].push('','','');
        Dayprocess ();
      }else if (dayofweek === 'Thu') {
          vm.days[0].push('','','','');
          Dayprocess ();
      }else if (dayofweek === 'Fri') {
          vm.days[0].push('','','','','');
          Dayprocess ();
      }else if (dayofweek === 'Sat') {
        for (let i = 1; i <= 7 ; i++) {
          vm.days[0].push(i);
          Dayprocess ();
        }        
      }

      // make shift arr
      let obj = {name: '', starttime : '', endtime : '', day: ''};
      for (let i = 0 ; i <= vm.days.length -1; i++) {
        for(let j = 1 ; j <= vm.days[i].length; j++) {
          vm.shifts[i].push(obj);
        }
      }

      // checkrow
      function row(num) {
        if (vm.days[num][0] === '' && vm.days[num][6] === '') {
        vm.days.splice(num , 1);
        vm.shifts.splice(num , 1);
        }
      }
      row(0);
      row(5);

    } ())

    this.$bus.$on('shift', (obj) => {
      this.tocar(obj);
    })

    this.$bus.$on('clearshift', () => {
      var vm = this;
      for(let i = 0 ; i <= vm.shifts.length -1; i++) {
        for (let j = 0 ; j <= vm.shifts[i].length -1; j++) {
          vm.shifts[i][j] = {name: '', starttime : '', endtime : '' ,day: ''};
        }
      }
    })

    this.$bus.$on('updateguide', (val) => {
      this.updateguide(val);
    })

  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">

  @import '../scss/base/var';
  @import '../scss/mixin/mixin';

  .calendar {
    display: inline-block;
    width: 60%;
    table {
      display: flex;
      flex-direction: column;
      justify-content: center;
      background-color: gray;
      margin: 0 20px;
      thead {
        tr {
          display: flex;
          justify-content: space-around;
          td {
            text-align: center;
          }
          .changeM {
            cursor: pointer;
            font-size: 50px;

            &:hover {
              color: hsl(59, 100%, 67%);
            }
          }
          .monthyear {
            font-size: 40px;
          }
          .weeks {
            margin: 0 auto;
          }
        }
        .monthrow {
          padding: 2%;
          background-color: $blue;

          td {
            font-size: 40px;
            color: white;
          }
        }
        .weeksrow {
          background-color: $blue;
          padding: 1.5% 0;
          td {
            font-size: 30px;
            color: white;
          }
        }
      }
      tbody {
        position: relative;
          @include guide;
          .guide {
            top: 50%;
            left: 50%;
            transform: translate(-50%,-22%);
          }
      }
    }
  }
  @include rwd (large) {
    .calendar {
      width: 55%;
      table thead {
        .monthrow td {
          font-size: $large-h2;
        }
        .weeksrow td {
          font-size: $large-h3;
        }
      }
    }
  }
</style>
