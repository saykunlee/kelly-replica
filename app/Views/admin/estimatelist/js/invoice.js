

Vue.prototype.$common = app_lib;
Vue.prototype.$app_estimate = app_estimate;
let app = new Vue({
  el: '#app',
  data: {
    action_type: 'insert',
    room_action_type: 'insert',
    commoncode_1: view_data.commoncode_1, // 
    commoncode_2: view_data.commoncode_2, // 
    commoncode_3: view_data.commoncode_3, // 침대타입
    commoncode_4: view_data.commoncode_4, // 뷰타입
    commoncode_5: view_data.commoncode_5, // 건물구분
    commoncode_6: '', // 뷰구분
    searched_rooms: {
      count_total: 0,
    },
    is_room_searched: false,
    estimate: [],
    rental_car_list: app_estimate.lib_rental_car_list,
    golf_club_cost_list: app_estimate.lib_golf_club_cost_list,
    exchange: app_estimate.lib_exchange,
    editorContent: '',

    list: [],

    //검색관련 변수 
    search: {
      sel_exp_ci_date: null,
      sel_exp_co_date: null,
      sel_room_type_no: 1,  // 검색할 룸타입을 받아올것.
      //sel_floor:
      sel_rooms: 1,
      sel_nights: 1,
      sel_guests: 4,
    },
    groupedEstimates: {},
    selectedImage: null,

  },

  computed: {
    resfee_type_nm() {
      // if estimate.room_estimate.room_checked[0].deposit_policy  is not null,emty and is not A
      if (app_estimate.lib_estimate.room_estimate.room_checked[0].deposit_policy != 'A') {
        return app_estimate.lib_estimate.room_estimate.room_checked[0].deposit_policy + '박 요금';
      } else {
        return app_estimate.lib_estimate.room_estimate.room_checked[0].accoms_type_nm;
      }

    },
    resfee_type_nm_text() {
      // if estimate.room_estimate.room_checked[0].deposit_policy  is not null,emty and is not A
      if (app_estimate.lib_estimate.room_estimate.room_checked[0].deposit_policy != 'A') {
        return app_estimate.lib_estimate.room_estimate.room_checked[0].deposit_policy + '박 요금';
      } else {
        return app_estimate.lib_estimate.room_estimate.room_checked[0].accoms_type_nm + ' 요금';
      }

    },
    //예약금 계산
    roomresfee_golf_sum() {
      let resfee = 0;
      let golf_sum = 0;
      if (app_estimate.lib_estimate.room_estimate.room_resfee_kr > 0) {
        resfee = app_estimate.lib_estimate.room_estimate.room_resfee_kr;
      } else {
        resfee = app_estimate.lib_estimate.room_estimate.room_resfee * this.exchange;
      }
      if (app_estimate.lib_estimate.golf_amount_kr > 0) {
        golf_sum = app_estimate.lib_estimate.golf_amount_kr;
      } else {
        golf_sum = app_estimate.lib_estimate.golf_amount * this.exchange;
      }
      return resfee + golf_sum;
    },
    computed_resfee() {
      if (app_estimate.lib_estimate.room_estimate.room_resfee_kr > 0) {
        return app_estimate.lib_estimate.room_estimate.room_resfee_kr;
      } else {
        return app_estimate.lib_estimate.room_estimate.room_resfee * this.exchange;
      }
    },
    computed_golf_amount() {
      if (app_estimate.lib_estimate.golf_amount_kr > 0) {
        return app_estimate.lib_estimate.golf_amount_kr;
      } else {
        return app_estimate.lib_estimate.golf_amount * this.exchange;
      }
   
    }



  },

  methods: {
    removeHtmlTags(str) {
      if (str) {
        const doc = new DOMParser().parseFromString(str, 'text/html');
        return doc.body.textContent || doc.body.innerText || "";
      }
      return str;
    },
    //app_estimate.lib_estimate.rental_car_estimate.forEach((estimate) => {
    groupEstimates() {
      const tempGroup = {};

      app_estimate.lib_estimate.rental_car_estimate.forEach((estimate) => {
        const key = `${estimate.car_no}-${estimate.name}`;

        if (!tempGroup[key]) {
          tempGroup[key] = {
            items: [],
            commonAttributes: {
              key_car_no: estimate.car_no,
              key_car_name: estimate.name,
              key_cost_per_night: estimate.cost_per_night,
              key_description: estimate.description
            }
          };
        }

        tempGroup[key].items.push(estimate);
      });

      this.groupedEstimates = Object.keys(tempGroup).map(key => {
        const group = tempGroup[key];
        const sumRentalAmount = group.items.reduce((acc, cur) => acc + parseInt(cur.rental_amount, 10), 0);
        const sumRentalDays = group.items.reduce((acc, cur) => acc + parseInt(cur.rental_days, 10), 0);
        const sumRental_Counts = group.items.reduce((acc, cur) => acc + parseInt(cur.rental_counts, 10), 0);

        return {
          groupKey: key,
          ...group.commonAttributes,
          key_sum_rental_amount: sumRentalAmount,
          key_sum_rental_days: sumRentalDays,
          key_sum_rental_counts: sumRental_Counts,
          items: group.items
        };
      });
    }
    ,
    getRowSpan(items) {
      // 모든 item과 각 item의 estimate_item_detail의 총 길이를 계산
      let totalLength = 0;
      for (const item of items) {
        totalLength++; // item 자체의 길이
        if (item.estimate_item_detail) {
          totalLength += item.estimate_item_detail.length; // 각 item의 estimate_item_detail 길이
        }
      }
      return totalLength;
    },
    //예약 가능한 객실 조회 
    async getavailableroom() {

      let params = new FormData();

      this.is_loding_list_1 = true;
      params.append("postdata", JSON.stringify(this.search));

      await axios
        .post('/api/estimate/getavailableroom', params)
        .then(response => {
          console.info('axios response :', response.data.result);

          if (response.data.status == 200) {
            //app_lib.resetObj(app_estimate.lib_estimate.room_estimate); // clear value
            this.searched_rooms = response.data.result.list;
            this.checkedRes = [];
            //app_estimate.lib_estimate.room_estimate.room_checked = [];
            //app_estimate.lib_estimate.room_estimate.room_amount = 0;
            //app_estimate.lib_estimate.room_amount = 0;
            //set default values
            //app_estimate.lib_estimate.room_estimate.res_info = response.data.result.params;
          } else {
            toastr.error('PMS', response.data.message);
          }

        })
        .catch(error => {
          console.info('error', 'API호출에 실패했습니다.' + error)
        })
        .finally(() => {
          this.is_loding_list_1 = false;
          this.is_room_searched = true;
        });

    },
    //견적마스터 생성
    async save_estimate() {

      //set customer's info
      app_estimate.lib_estimate.room_estimate.guest_name = app_estimate.lib_estimate.guest_name;
      app_estimate.lib_estimate.room_estimate.res_info.guest_name = app_estimate.lib_estimate.guest_name;
      app_estimate.lib_estimate.room_estimate.guest_hp = app_estimate.lib_estimate.guest_hp;
      app_estimate.lib_estimate.room_estimate.guest_email = app_estimate.lib_estimate.guest_email;
      app_estimate.lib_estimate.room_estimate.air_schedule = app_estimate.lib_estimate.air_schedule;
      app_estimate.lib_estimate.room_estimate.checkin_local = app_estimate.lib_estimate.checkin_local;
      app_estimate.lib_estimate.room_estimate.res_guest_class_cd = app_estimate.lib_estimate.res_guest_class_cd;
      delete_estimate_return = [];

      //console.log("🚀 ~ file: write.js:91 ~ save_estimate ~  app_estimate.lib_estimate.room_estimate.res_info:",  app_estimate.lib_estimate.room_estimate)

      let params = new FormData();
      params.append("postdata", JSON.stringify(app_estimate.lib_estimate));

      params.append("action_type", this.action_type);

      app_lib.is_loding_list_1 = true;
      let callback_type = 'stay';

      await axios
        .post('/api/estimate/save_estimate', params)
        .then(response => {
          console.info('axios response :', response.data);
          this.axios_response = response.data;
          if (response.data.status == 200) {

            $('.modal').modal('hide');
            toastr.success(app_lib.toastr_project, response.data.message);
            if (this.action_type == 'insert') {
              setTimeout(() => app_lib.back_to_list(), 1000);
            }

          } else {
            toastr.error(app_lib.toastr_project, response.data.message);

          }
        })
        .catch(error => {
          toastr.error(app_lib.toastr_project, 'API호출에 실패했습니다.' + error);
        })
        .finally(() => {
          app_lib.is_loding_list_1 = false;
        });
    },


    // 화면 초기화 값 전달 2196f3
    async front_construct() {
      let params = new FormData();

    },
  },


  mounted: function () {
    this.estimate = app_estimate.lib_estimate;
    $('#invoice_modal').on('show.bs.modal', async function (event) {
      let element = document.getElementById('invoice');
      const canvas = await html2canvas(element);
      // console.log("🚀 ~ file: invoice.php:511 ~ $ ~ canvas:", canvas)
      canvas.toBlob((blob) => {
        let url = URL.createObjectURL(blob);
        let image = document.getElementById('invoiceImage');
        //console.log("🚀 ~ file: invoice.php:517 ~ canvas.toBlob ~ image:", image)
        // Assign blob URL to image and display it
        image.src = url;
      }, 'image/jpeg');
    })
    this.groupEstimates();
  },
  created() {
    this.action_type = view_data.action_type;
    if (this.action_type == 'update') {
      app_estimate.lib_estimate = view_data.data;
      // console.log("🚀 ~ file: write.js:328 ~ created ~ app_estimate.lib_estimate:", app_estimate.lib_estimate)
    } else {
      app_estimate.lib_estimate.mem_id = null;
    }
    this.front_construct()

  }
})

