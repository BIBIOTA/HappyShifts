<template>
    <tr class="daysrow">
      <td class="days" v-for="(data, index) in day" :key="index" @click="selectdate(data)" @mouseover="hover" @mouseout="hoverout">
        {{data}}
      </td>
    </tr>
    <tr class="shiftsrow">
      <td class="shifts" v-for="(shift, index) in shifts" :key="index" @click="selectdateshift($event)"  @mouseover="hover" @mouseout="hoverout">
        {{shift.name}}
        <br>
        <p>{{shift.starttime}}</p>
        <p v-show="shift.name != ''">~</p>
        <p>{{shift.endtime}}</p>
      </td>
    </tr>
</template>

<script>
export default {
  name: 'cdays',
  props : ['day','shifts'],
  emits: ["update"],
  data () {
    return {
      nowdate: 1,
    }
  },
  methods: {
    hover (e) {
      for (let i = 0; i <= document.querySelectorAll('.days').length -1 ; i++) {
        if ((e.target === document.querySelectorAll('.days')[i] || e.target === document.querySelectorAll('.shifts')[i]) && document.querySelectorAll('.days')[i].innerHTML != '') {
          document.querySelectorAll('.days')[i].classList.add('hover');
          document.querySelectorAll('.shifts')[i].classList.add('hover');
        }
      }
    },
    hoverout (e) {
      for (let i = 0; i <= document.querySelectorAll('.days').length -1 ; i++) {
        if ((e.target === document.querySelectorAll('.days')[i] || e.target === document.querySelectorAll('.shifts')[i]) && document.querySelectorAll('.days')[i].innerHTML != '') {
          document.querySelectorAll('.days')[i].classList.remove('hover');
          document.querySelectorAll('.shifts')[i].classList.remove('hover');
        }
      }
    },
    selectdateshift (e) {
        let shiftslength = document.querySelectorAll('.shifts').length;
        for (let i = 0; i <= shiftslength; i++) {
          if (e.target === document.querySelectorAll('.shifts')[i]) {
            document.querySelectorAll('.days')[i].click();
          }
        }
    },
    selectdate(day) {
      var vm = this;
      if (day != '') {
        vm.nowdate = day;
        vm.updateclass();
      }
    },
    nowindex(nowindex) {
      this.$emit('update',nowindex);
    },
    updateclass() {
      var vm = this;
      let datelength = document.querySelectorAll('.days').length;
      for (let i = 0; i <= datelength -1 ; i++) {
        document.querySelectorAll('.days')[i].classList.remove('nowday');
        document.querySelectorAll('.shifts')[i].classList.remove('nowshift');
        if (document.querySelectorAll('.days')[i].innerHTML == vm.nowdate) {
          vm.nowindex(i);
          document.querySelectorAll('.days')[i].classList.add('nowday');
          document.querySelectorAll('.shifts')[i].classList.add('nowshift');
        }
      }
    },
    plusdate (day) {
      var vm = this;
      vm.nowdate = day + 1;
    }
  },
  mounted() {
    this.$bus.$on('nextcar', (day) => {
      this.plusdate(day);
    });
  },
  updated() {
    var vm = this;
    vm.updateclass();
  },
}
</script>

<style scoped lang="scss">

    @import '../scss/base/var';
    @import '../scss/mixin/mixin';

    tbody {
        width: 100%;
        tr {
          width: 100%;
          display: flex;
          justify-content: space-around;
          td {
            width: calc(100% / 7);
            font-size: 30px;
            text-align: center;
          }
        }
        .daysrow, .shiftsrow {
          background-color: #F2F2F2;
          .days {
            position: relative;
            border-top : 1px solid white;
          }
          .hover {
            background-color: #e2e2e2;
          }
          .shifts {
            border-bottom: 1px solid white;
            p {
              display: inline-block;
            }
          }
          .days, .shifts {
            cursor: pointer;
            border-left : 1px solid white;
            border-right : 1px solid white;
          }
          .nowday {
            border-top : 1px solid $blue;
            border-left : 1px solid $blue;
            border-right : 1px solid $blue;
            background-color: white;
          }
          .nowshift {
            border-left : 1px solid $blue;
            border-right : 1px solid $blue;
            border-bottom : 1px solid $blue;
            background-color: white;
          }
        }
        .nowedit {
          background-color: #f9f9f9;
        }
    }
</style>
