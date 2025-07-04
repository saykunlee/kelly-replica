const { createApp } = Vue;

createApp({
  data() {
    return {
      message: 'Hello from Vue.js with local file!',
      list: [],
      search: {
        category_id: '',
        parent_id: '',
        is_active: ''
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
    hello: function () {
      console.log('hello');
    },
    ini_dt(_dtid) {
      let dtId = "#" + _dtid;
      let utDt;

      if ($.fn.DataTable.isDataTable(dtId)) {
        $(dtId).DataTable().clear().destroy();
      }

      // 테이블 요소가 존재하는지 확인
      if ($(dtId).length) {
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
            url: '/api/menu-api/get-menu-list', // Replace with your API endpoint
            type: 'POST',
            data: function (d) {
              // DataTables가 서버로 전송하는 기본 매개변수 외에 추가 매개변수 추가
              return $.extend({}, d, {
                category_id: this.search.category_id,
                parent_id: this.search.parent_id,
                is_active: this.search.is_active
              });
            }.bind(this), // Vue 인스턴스의 this를 바인딩
            dataSrc: function (response) {
              console.log(response); // API 호출 결과를 콘솔에 출력
              return response.data; // 'data' 키를 사용하여 데이터 반환
            },
            error: function (xhr, status, error) {
              console.error('API 호출 중 오류 발생:', status, error); // 오류 발생 시 콘솔에 출력
            }
          },
          columns: [

            {
              data: null, // 데이터 소스가 필요하지 않음
              title: '순서',
              width: '45px',
              className: 'tx-center-f',
              render: function (data, type, row, meta) {
                // 현재 페이지의 시작 인덱스를 가져와서 전체 레코드에서의 순서 값을 계산
                return meta.settings._iDisplayStart + meta.row + 1;
              }
            },
            { data: 'title', title: '제목', width: '100px', className: 'tx-left' },
            { data: 'url', title: 'URL', width: '100px', className: 'tx-left' },
            { data: 'route', title: '라우트', width: '300px', className: 'tx-left' },
            { data: 'icon', title: '아이콘', width: '50px', className: 'tx-left' },
            { data: 'parent_title', title: '상위 메뉴', width: '100px', className: 'tx-left' },
            { data: 'category_name', title: '카테고리', width: '100px', className: 'tx-left' },
            { data: 'order', title: '순서', width: '50px', className: 'tx-center-f' },
            { data: 'created_by', title: '생성자', width: '100px', className: 'tx-left' },
            { data: 'created_at', title: '생성일', width: '100px', className: 'tx-center-f' },
            { data: 'updated_by', title: '수정자', width: '100px', className: 'tx-left' },
            { data: 'updated_at', title: '수정일', width: '100px', className: 'tx-center-f' },
            { data: 'is_active', title: '활성화', width: '50px', className: 'tx-center-f' },
            {
              data: null,
              title: '',
              width: '50px',
              className: 'tx-center-f',
              render: function (data, type, row) {
                return '<a style="color: #596882;" href="javascript:void(0);" onclick="go_detail(\'list\', ' + data.no + ');"><i data-feather="more-vertical"></i></a>';
              }
            }
          ],
          language: {
            searchPlaceholder: 'Search...',
            sSearch: '',
            lengthMenu: '_MENU_ items/page',
          },
          headerCallback: function (thead, data, start, end, display) {
            $(thead).find('th').addClass('tx-center-f');
          },
          createdRow: function (row, data, dataIndex) {
            $(row).find('td').addClass('valign-middle-f');
          }
        });

        // DataTables 이벤트 리스너 추가
        $(dtId).on('processing.dt', function (e, settings, processing) {
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
      } else {
        console.error('Table element not found: ' + dtId);
      }
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.$nextTick(() => {
        this.ini_dt('list');
      });
    });
  }
}).mount('#app');