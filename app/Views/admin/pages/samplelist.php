<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="container pd-x-0 tx-13">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1 mg-b-5">
            <li class="breadcrumb-item"><a href="#">페이지관리</a></li>
            <li class="breadcrumb-item active" aria-current="page">메뉴관리</li>
        </ol>
    </nav>

    <h4 class="mg-b-25">메뉴관리</h4>

    <div id="app">
        <!-- <hr class="mg-b-20"> -->
        <!-- tab area -->
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-line nav-line-profile mg-b-30">
                    <li class="nav-item">
                        <a href="" class="nav-link d-flex align-items-center active">사이드</a>
                    </li>
                    <li class="nav-item">
                        <a href="" class="nav-link">탑 </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- // tab area -->

        <!-- search area -->
        <div class="search_area">
            <div class="row">

                <div class="form-group col-lg-3">
                    <label for="formGroupExampleInput" class="d-block">견적일자</label>
                    <input type="date" class="form-control estimate_date" placeholder="시작 선택" v-model="search.sdate">
                </div>

                <div class="form-group col-lg-3">
                    <label for="formGroupExampleInput" class="d-block">&nbsp;</label>
                    <input type="date" class="form-control estimate_date" placeholder="시작 선택" v-model="search.edate">
                </div>
                <div class="form-group col-lg-3">
                    <label for="formGroupExampleInput2" class="d-block">견적고객명</label>
                    <input type="search" class="form-control" placeholder="견적고객명 검색" v-model="search.guest_name">
                </div>

                <div class="form-group col-lg-2">
                    <label for="formGroupExampleInput" class="d-block">상태</label>

                    <select class="custom-select" v-model="search.status">
                        <option value="">전체</option>
                        <option value="RR">대기</option>
                        <option value="CR">확정</option>
                        <option value="RC">취소</option>
                        <option value="RD">삭제</option>
                        <option value="NS">노쇼</option>
                    </select>
                </div>
                <div class="form-group col-lg-1">
                    <label for="formGroupExampleInput" class="d-block">&nbsp;</label>

                    <a class="btn btn-primary" @click="ini_dt('list')" href="javascript:void(0);"> <i data-feather="search"></i> </a>
                </div>

            </div>
        </div>

        <!-- DataTable -->
        <div class="row">
            <div class="col-lg-12">
                <div style="overflow-x:auto; padding-bottom: 15px;">
                    <table id="list" class="table cell-border hover" style="table-layout: fixed" width="1131px">
                        <thead>
                            <tr>
                                <th>견적번호</th>
                                <th>상태</th>
                                <th>예약자명</th>
                                <th>숙소명</th>
                                <th>체크인</th>
                                <th>체크아웃</th>
                                <th>박수</th>
                                <th>객실수</th>
                                <th>금액</th>
                                <th>예약일</th>
                                <th>useing</th>
                                <th>status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables가 데이터를 여기에 바인딩합니다 -->
                        </tbody>
                        <tfoot>
                            <tr class="bg-white">
                                <td colspan="13" class="mg-15-f">
                                    <div id="placeholder" class="placeholder-paragraph pd-15">
                                        <div class="line"></div>
                                        <div class="line"></div>
                                        <div class="line"></div>
                                        <div class="line"></div>
                                        <div class="line"></div>
                                        <div class="line"></div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>


</div>
<!-- 여기에 Admin Dashboard 내용을 추가하세요 -->

<script>
    const {
        createApp
    } = Vue;

    createApp({
        data() {
            return {
                message: 'Hello from Vue.js with local file!',
                list: [],
                search: {
                    sdate: '2024-08-01',
                    edate: '2024-08-03',
                    guest_name: '',
                    status: ''
                }
            };
        },
        components: {
            'my-component': {
                template: `
                    <div>
                        <slot></slot>
                    </div>
                `
            }
        },
        methods: {
            hello: function() {
                console.log('hello');
            },
            ini_dt(_dtid) {
                let dtId = "#" + _dtid;
                let utDt;

                if ($.fn.DataTable.isDataTable(dtId)) {
                    $(dtId).DataTable().clear().destroy();
                }

                utDt = $(dtId).DataTable({
                    dom: 'Bfrtip',
                    searching: true,
                    rowId: 'extn',
                    aaSorting: [],
                    serverSide: true, // 서버 사이드 처리 활성화
                    processing: false, // 처리 중 표시 활성화
                    buttons: ['pageLength', 'excelHtml5', 'pdfHtml5', 'print'],
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, 'All'],
                    ],
                    ajax: {
                        url: '/api/estimate-api/get-estimate-list', // Replace with your API endpoint
                        type: 'POST',
                        data: function(d) {
                            // DataTables가 서버로 전송하는 기본 매개변수 외에 추가 매개변수 추가
                            return $.extend({}, d, {
                                sdate: this.search.sdate,
                                edate: this.search.edate,
                                guest_name: this.search.guest_name,
                                status: this.search.status
                            });
                        }.bind(this), // Vue 인스턴스의 this를 바인딩
                        dataSrc: function(response) {
                            console.log(response); // API 호출 결과를 콘솔에 출력
                            return response.data; // 'data' 키를 사용하여 데이터 반환
                        },
                        error: function(xhr, status, error) {
                            console.error('API 호출 중 오류 발생:', status, error); // 오류 발생 시 콘솔에 출력
                        }
                    },
                    columns: [{
                            data: 'no',
                            title: '견적번호',
                            width: '80px',
                            className: 'tx-center-f'
                        },
                        {
                            data: 'estimate_status',
                            title: '상태',
                            width: '80px',
                            className: 'tx-center-f'
                        },
                        {
                            data: 'guest_name',
                            title: '예약자명',
                            width: '100px',
                            className: 'tx-left'
                        },
                        {
                            data: 'room[0].room_type_nm_short',
                            title: '숙소명',
                            width: '50px',
                            className: 'tx-left'
                        },
                        {
                            data: 'room[0].exp_ci_date',
                            title: '체크인',
                            width: '100px',
                            className: 'tx-center-f'
                        },
                        {
                            data: 'room[0].exp_co_date',
                            title: '체크아웃',
                            width: '100px',
                            className: 'tx-center-f'
                        },
                        {
                            data: 'room[0].nights',
                            title: '박수',
                            width: '50px',
                            className: 'tx-center-f'
                        },
                        {
                            data: 'room[0].rooms',
                            title: '객실수',
                            width: '50px',
                            className: 'tx-center-f'
                        },
                        {
                            data: 'total_amount',
                            title: '금액',
                            width: '100px',
                            className: 'text-right'
                        },
                        {
                            data: 'insert_date',
                            title: '예약일',
                            width: '100px',
                            className: 'tx-center-f'
                        },
                        {
                            data: 'useing',
                            title: 'useing',
                            width: '80px',
                            className: 'tx-center-f',
                            visible: false
                        },
                        {
                            data: 'estimate_status',
                            title: 'status',
                            width: '80px',
                            className: 'tx-center-f',
                            visible: false
                        },
                        {
                            data: null,
                            title: '',
                            width: '50px',
                            className: 'tx-center-f',
                            render: function(data, type, row) {
                                return '<a style="color: #596882;" href="javascript:void(0);" onclick="go_detail(\'list\', ' + data.no + ');"><i data-feather="more-vertical"></i></a>';
                            }
                        }
                    ],
                    language: {
                        searchPlaceholder: 'Search...',
                        sSearch: '',
                        lengthMenu: '_MENU_ items/page',
                    },
                    headerCallback: function(thead, data, start, end, display) {
                        $(thead).find('th').addClass('tx-center-f');
                    },
                    createdRow: function(row, data, dataIndex) {
                        $(row).find('td').addClass('valign-middle-f');
                    }
                });

                // DataTables 이벤트 리스너 추가
                $(dtId).on('processing.dt', function(e, settings, processing) {
                    if (processing) {
                        /*   $('#placeholder').show(); */
                        $(dtId + ' tbody').hide();
                        $(dtId + ' tfoot').show();
                    } else {
                        /* $('#placeholder').hide(); */
                        $(dtId + ' tbody').show();
                        $(dtId + ' tfoot').hide();
                    }
                });

                feather.replace();
            }
        },
        mounted() {
            this.ini_dt('list');
        }
    }).mount('#app');
</script>

<?= $this->endSection() ?>