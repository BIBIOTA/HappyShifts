<template>
    <div class="editbar">
        <div class="title">
            <h2>班別管理</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <td>班別</td>
                    <td>上班時間</td>
                    <td>編輯/刪除</td>
                </tr>
            </thead>
            <tbody id="tbody">
                <tr v-for="(shift,index) in shifts" :key="index">
                    <div class="guide guide1" v-show="index === 0 && guide === 1">
                        <span class="arrow">🡅</span>
                        <br />
                        點擊班別，
                        <br />
                        可以新增到行事曆哦 !
                        <button class="btn" @click="iknow"><p>知道了</p></button>
                    </div>
                <td class="shift" v-if="editname === index"><input type="text" v-model="shift.name" class="shift shift_text"></td>
                <td class="shift shiftshow" v-else-if="editname != index" @click="addcar(shift.name,shift.starttime,shift.endtime)">
                    {{shift.name}}
                    </td>
                <td class="times" v-if="editname === index">
                    <select id="starttime">
                        <option v-for="(starttime,index) in timearr" :key="index" :value="starttime" :selected="shift.starttime == starttime">{{starttime}}</option>
                    </select>
                    ~
                        <select id="endtime">
                        <option v-for="(endtime,index) in timearr" :key="index" :value="endtime" :selected="shift.endtime == endtime">{{endtime}}</option>
                    </select>
                    </td> 
                <td class="times" v-else-if="editname != index">{{shift.starttime}} ~ {{shift.endtime}}</td> 
                <td class="icon" @click.capture="edit($event,index)">
                    <i class="fas fa-check" style="display: none"></i>
                    <i class="fas fa-edit"></i>
                    <i class="fas fa-trash"></i>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="add">
            <button @click="clear">
                <h3>清空行事曆</h3>
            </button>
            <button @click="add">
                <h3>新增班別</h3>
            </button>
        </div>
    </div>
</template>

<script>
import * as moment from "moment/moment";

export default {
    name: 'editbar',
    props : ['guide'],
    emit : ['updateguide'],
    data () {
        return {
            shifts: [{name: 'A', starttime: '00:00', endtime: '12:00'}],
            editname : 'default',
            timearr : [],
        }
    },
    methods: {
        iknow () {
            if (this.$props.guide === 1) {
                document.querySelector('.guide1').style.display="none";
                this.$emit('updateguide',2);
                document.querySelector('#tbody').style.overflow = "auto";
            }
        },
        clear () {
            this.$bus.$emit('clearshift');
        },
        add () {
            var vm = this;
            vm.iknow();
            let arr = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",];
            var shiftname;
            for (let i = 0; i <= arr.length -1; i++) {
                let index = vm.shifts.length -1;
                if (vm.shifts[index].name === arr[i]) {
                    shiftname = arr[i + 1];
                    break
                }else if (arr[i + 1] === 'Z' ) {
                    shiftname = 'A'
                    break
                }else if(vm.shifts[index].name != arr[i]){
                    shiftname = 'A'
                }
            }
            let obj = {name : shiftname, starttime: '00:00', endtime: '12:00'};
            vm.shifts.push(obj);
        },
        addcar (name,starttime,endtime) {
            let obj = {name: name, starttime : starttime, endtime : endtime, day: ''};
            this.$bus.$emit('shift', obj);
            this.iknow ()
        },
        edit (e,index) {
            var vm = this;
            let checklength = document.querySelectorAll('.fa-check').length;
            for (let i = 0; i <= checklength -1; i++) {
                document.querySelectorAll('.fa-check')[i].style.display = "none";
                document.querySelectorAll('.fa-edit')[i].style.display = "block";
            }
            let edit = e.currentTarget.querySelectorAll('svg')[1];
            let editpath = e.currentTarget.querySelectorAll('path')[1];
            let trash = e.currentTarget.querySelectorAll('svg')[2];
            let trashpath = e.currentTarget.querySelectorAll('path')[2];
            let check = e.currentTarget.querySelectorAll('svg')[0];
            let checkpath = e.currentTarget.querySelectorAll('path')[0];
            if (e.target == edit || e.target == editpath) {
                check.style.display = "block";
                edit.style.display = "none";
                vm.editname = index;
            }else if(e.target == trash || e.target == trashpath) {
                vm.shifts.splice(index, 1);
            }else if (e.target == check || e.target == checkpath) {
                vm.shifts[index].starttime = document.getElementById('starttime').value;
                vm.shifts[index].endtime = document.getElementById('endtime').value;
                check.style.display = "none";
                edit.style.display = "block";
                vm.editname = 'default';
            }
        }
    },
    mounted() {
        var vm = this;
        let timearr = [];
        for(let h = 0; h <= 23 ; h++) {
            let hh;
            if (h <= 9) {
                hh = '0' + h;
            }else{
                hh = h;
            }
            for (let j = 1 ; j <= 2; j++) {
                let mm;
                if (j === 1) {
                    mm = '00';
                }else{
                    mm = '30';
                }
                let time = moment().format(`${hh}:${mm}`);
                timearr.push(time);
            }
        }
        vm.timearr = timearr;
    },
}
</script>

<style lang="scss">

    @import '../scss/base/var';
    @import '../scss/mixin/_mixin';

    .editbar {
        display: inline-block;
        width: 35%;
        background-color: #EBEBEB;
        position: relative;
        max-height: 975px;
        .title {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: $blue;

            h2 {
                margin: 1rem;
                color: white;
            }
        }
        table {
            display: flex;
            flex-direction: column;
            thead {
                tr {
                    background-color: $blue;
                    padding: 1.5%;
                    margin-bottom: 0;
                    td {
                        font-size: 30px;
                        color: white;
                        text-align: center;
                    }
                    td:nth-child(1), td:nth-child(3) {
                        width: 30%;
                    }
                    td:nth-child(2) {
                        width: 40%;
                    }
                }
            }
            tbody {
                height: 460px;
            }
            tr {
                display: flex;
                justify-content: space-around;
                align-items: center;
                background-color: #fff;
                margin-bottom: 25px;
                padding: 2%;
                position: relative;
                @include guide;

                td {
                    display: flex;
                    justify-content: space-around;
                }
                .shift {
                    font-size: 40px;
                }
                .shiftshow {
                    @include btn(white,#f6fafe);
                }
                .shift_text {
                    width: 50% !important;
                    text-align: center;
                }
                .shift, .icon {
                    width : 30%;
                }
                .times {
                    width: 40%;
                    select {
                        font-size: 30px;
                    }
                }
                .times, .icon {
                    font-size: 30px;
                    svg {
                        cursor: pointer;
                        margin: 0 auto;
                    }
                }
            }
        }
        .add {
            position: absolute;
            width: 100%;
            bottom: 5%;
            display: flex;
            justify-content: space-around;
            background-color: #EBEBEB;
            button {
                @include btn(white,#f6fafe);
                padding: 0 7%;
            }
        }
    }
    @include rwd (large) {
        .editbar {
            width: 45%;
            .title h2 {
                font-size: $large-h2;
            }
            table thead tr {
                td {
                font-size : $large-h3;
                }
            }
            table tr .shift {
                font-size: $large-h2;
            }
        }
    }
</style>