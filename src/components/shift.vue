<template>
    <tr>
        <td class="shift">A</td>
        <td class="times">12:00 ~ 12:00</td> 
        <td class="icon">
            <i class="fas fa-edit"></i>
            <i class="fas fa-trash"></i>
        </td>
    </tr>
</template>

<script>
export default {
    name: 'shift'
}
</script>

<style lang="scss">
    tbody {
        width: 100%;
        tr {
            display: flex;
            justify-content: space-around;
            align-items: center;
            background-color: #fff;
            td {
                display: flex;
                justify-content: space-around;
            }
            .shift {
                font-size: 40px;
            }
            .shift, .icon {
                width : 30%;
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
</style>