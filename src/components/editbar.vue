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
                    <div class="guide guide1" v-show="haslogin === false && guide === 1">
                        <span class="arrow arrow_first">🡅</span>
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
            <button @click="clear" class="clear">
                <h3>清空行事曆</h3>
            </button>
            <button @click="add" class="addbtn">
                <h3>新增班別</h3>
            </button>
        </div>
    </div>
    <div class="mobileeditbar" :class="{transition: mobileedit}">
        <div class="title">
            <h3>點擊班別到指定的日期</h3>
        </div>
        <div class="shiftsbox">
            <div class="shift" @click="clear">
                <div class="clear">清空行事曆</div>
            </div>
            <div v-for="(shift,index) in shifts" :key="index" class="shift" @click="addcar(shift.name,shift.starttime,shift.endtime)">
                <div class="shiftname">{{shift.name}}</div>
                <div class="time">{{shift.starttime}}~{{shift.endtime}}</div>
            </div>
        </div>
        <div class="edit">
            <div class="editbtn" @click="closeedit">
                <i class="fas fa-times-circle"></i>
                <p>關閉</p>
            </div>
            <div class="editbtn" @click="opensetting">
                <i class="fas fa-cog"></i>
                <p>設定</p>
            </div>
        </div>
    </div>
    <div class="editbar editmobileshift">
        <div class="close" @click="closeeditbar">
            <i class="fas fa-arrow-alt-circle-down"></i>
        </div>
        <div class="title">
            <h2>班別管理</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <td>班別</td>
                    <td>上班時間</td>
                    <td>刪除</td>
                </tr>
            </thead>
            <tbody id="tbody">
                <tr v-for="(shift,index) in shifts" :key="index">
                <td class="shift"><input type="text" v-model="shift.name" class="shift shift_text" @change="updateshiftlist(index)"></td>
                <td class="times">
                    <select class="starttime" @change="editshift(index)">
                        <option v-for="(starttime,index) in timearr" :key="index" :value="starttime" :selected="shift.starttime == starttime">{{starttime}}</option>
                    </select>
                    ~
                    <select class="endtime" @change="editshift(index)">
                        <option v-for="(endtime,index) in timearr" :key="index" :value="endtime" :selected="shift.endtime == endtime">{{endtime}}</option>
                    </select>
                    </td> 
                <td class="icon" @click.capture="trash(index)">
                    <i class="fas fa-trash"></i>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="add">
            <button @click="add" class="addbtn">
                <h3>新增班別</h3>
            </button>
        </div>
    </div>
</template>

<script>
import * as moment from "moment/moment";
export const API_URL = process.env.VUE_APP_API_URL;

export default {
    name: 'editbar',
    props : ['guide','mobileedit','haslogin'],
    emit : ['updateguide'],
    data () {
        return {
            shifts: [],
            editname : 'default',
            timearr : [],
        }
    },
    watch : {
        haslogin : function () {
            var vm = this;
            if (vm.haslogin === true) {
                vm.getshiftlist()
            }else{
                vm.shifts = [{name: 'A', starttime: '00:00', endtime: '12:00'}];
            }
        }
    },
    methods: {
        trash(index) {
            this.deleteshiftlist(index);
            this.shifts.splice(index, 1);
        },
        editshift (index) {
            var vm = this;
            vm.shifts[index].starttime = document.querySelectorAll('.starttime')[index].value;
            vm.shifts[index].endtime = document.querySelectorAll('.endtime')[index].value;
            vm.updateshiftlist(index);
        },
        closeeditbar () {
            document.querySelector('.editmobileshift').classList.remove('transition');
        },
        opensetting () {
            document.querySelector('.editmobileshift').classList.add('transition');
        },
        closeedit () {
            this.$emit('updateedit',false);
        },
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
            if (vm.shifts.length === 0) {
                shiftname = 'A'
            }else{
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
            }
            let obj = {name : shiftname, starttime: '00:00', endtime: '12:00'};
            vm.shifts.push(obj);
            if (this.haslogin === true) {
                vm.uploadshifts();
            }
        },
        uploadshifts () {
            var vm = this;
            let data = JSON.stringify(vm.shifts);
            this.$axios.get(API_URL+'/api/uploadshiftlist', { params: data})
            .then( (res)=> {
                console.log(res.data.id);
                vm.shifts[vm.shifts.length -1].id = res.data.id;
            })
            .catch( (res)=> {
                console.log(res);
            })
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
                if (this.haslogin === true) {
                    vm.deleteshiftlist(index);
                }
                vm.shifts.splice(index, 1);
            }else if (e.target == check || e.target == checkpath) {
                vm.shifts[index].starttime = document.getElementById('starttime').value;
                vm.shifts[index].endtime = document.getElementById('endtime').value;
                check.style.display = "none";
                edit.style.display = "block";
                vm.editname = 'default';
                if (this.haslogin === true) {
                    vm.updateshiftlist(index);
                }
            }
        },
        getshiftlist () {
            this.$axios.get(API_URL+'/api/getshiftlist')
            .then( (res)=> {
                console.log(res.data);
                if (res.data.length === 0) {
                    this.shifts = [{name: 'A', starttime: '00:00', endtime: '12:00'}];
                }else{
                    this.shifts = res.data;
                }
            })
            .catch( (res)=> {
                console.log(res);
            })
        },
        deleteshiftlist (index) {
            this.$axios.get(API_URL+'/api/deleteshiftlist' , {params: this.shifts[index]})
            .then( (res)=> {
                console.log(res);
            })
            .catch( (res)=> {
                console.log(res);
            })
        },
        updateshiftlist (index) {
            console.log(this.shifts[index]);
            this.$axios.get(API_URL+'/api/updateshiftlist' , {params: this.shifts[index]})
            .then( (res)=> {
                console.log(res);
            })
            .catch( (res)=> {
                console.log(res);
            })
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
                .icon {
                    svg:hover {
                        color: $green;
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
            bottom: 5%;
            width: 100%;
            display: flex;
            justify-content: space-around;
            background-color: #EBEBEB;
            button {
                @include btn(white,#f6fafe);
                padding: 0 7%;
            }
        }
    }
    .mobileeditbar {
        display: none;
    }
    .editmobileshift {
        display: none;
    }
    @include rwd (large) {
        .editbar {
            width: 45%;
            .title h2 {
                font-size: $large-h2;
            }
            table tbody {
                height: 400px;
                overflow-y: auto;
            }
            table thead tr {
                td {
                font-size : $large-h3;
                }
            }
            table tr {
                .times, .icon {
                    font-size: 25px;
                }
                .shift {
                    font-size: $large-h2;
                }
            }
        }
    }
    @include rwd (medium) {
        .editbar {
            position: fixed;
            z-index: 1;
            bottom : 0;
            transform: translateY(100%);
            width: 100%;
            height: 100vh;
            .close {
                position: absolute;
                right: 5%;
                bottom: 5%;
                cursor: pointer;
                z-index: 1;

                svg {
                    font-size: 80px;
                }
                &:hover svg {
                    color: $green;
                }
            }
            table {
                tbody {
                    height: 55vh;
                    overflow-y: auto;
                }
                tr {
                    .times, .icon {
                        font-size: 30px;
                    }
                    .guide {
                        display: none;
                    }
                    .shiftshow {
                        border: none;
                        box-shadow: none;
                        border-radius: 0;
                    }
                }
            }
            .add {
                height: 20vh;
                .clear {
                    display: none;
                }
                .addbtn {
                    position: absolute;
                    bottom: 5%;
                    width: 50%;
                }
            }
        }
        .mobileeditbar {
            display: block;
            width: 100%;
            position: fixed;
            background-color: white;
            bottom: 0;
            transform: translateY(100%);
            transition: transform .5s ease;
            .title {
                text-align: center;
                color: white;
                background-color: $blue;
                h3 {
                    margin: 0;
                }
            }
        }
        .editmobileshift {
            display: block;
            transition: transform .5s ease;
        }
        .transition {
            transform: translateY(0);
        }
        .shiftsbox {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            background-color: white;
            padding: 2%;
            max-height: 170px;
            overflow-y: auto;
            .shift {
                @include btn(white,#f6fafe);
                width: 15%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                margin: 1% 2%;
                padding: 2%;

                & > * {
                    font-size: 20px;
                }
            }
        }
        .edit{
            display: flex;
            justify-content: space-around;

            .editbtn {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                cursor: pointer;

                svg {
                    font-size: 30px;
                    color: $blue;
                }

                &:hover svg {
                    color: $green;
                }
            }
        }
    }
    @include rwd (small) {
        .shiftsbox .shift {
            width: 25%;
        }
    }
    @include rwd (xsmall) {
        .editbar {
            table tr .times {
                font-size: 20px;
                line-height: 40px;
                select {
                    font-size: 20px;
                    height: 40px;
                }
            }
            .add .addbtn {
                width: 40%;
                padding: 0;
            }
            .close svg {
                font-size: 65px;
            }
        }

        .shiftsbox .shift {
            width: 35%;
        }
    }
</style>