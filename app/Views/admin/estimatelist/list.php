	<style>
		.estimate_date {
			min-width: 95%;

		}
	</style>

	<!-- managermtype -->
	<div id="app">
		<!-- search area -->
		<hr class="mg-b-20">

		<div class="search_area">
			<div class="row">

				<div class="form-group col-lg-3">
					<label for="formGroupExampleInput" class="d-block">견적일자</label>
					<!-- <input type="date" class="form-control estimate_date" placeholder="시작 선택" @change="filterColumn_range('list',12,search.sdate,search.edate)" v-model="search.sdate"> -->
					<input type="date" class="form-control estimate_date" placeholder="시작 선택" v-model="search.sdate">
				</div>

				<div class="form-group col-lg-3">
					<label for="formGroupExampleInput" class="d-block">&nbsp;</label>
					<!-- <input type="date" class="form-control estimate_date" placeholder="시작 선택" @change="filterColumn_range('list',12,search.sdate,search.edate)" v-model="search.edate"> -->
					<input type="date" class="form-control estimate_date" placeholder="시작 선택" v-model="search.edate">
				</div>
				<div class="form-group col-lg-3">
					<label for="formGroupExampleInput2" class="d-block">견적고객명</label>
					<input type="search" class="form-control" placeholder="견적고객명 검색" @keydown="filterColumn('list',3,search.guest_name)" v-model="search.guest_name">
				</div>

				<div class="form-group col-lg-2">
					<label for="formGroupExampleInput" class="d-block">상태</label>

					<select class="custom-select" @change="filterColumn('list',8,search.status)" v-model="search.status">

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

					<a class="btn  btn-primary" @click="get_new_list()" href="javascript:void(0);"> <i data-feather="search"></i> </a>
				</div>

			</div>
		</div>

		<!-- DataTable -->
		<div v-if="$common.is_loding_list_1" class="row">
			<div class="col-lg-12">
				<div class="placeholder-paragraph mg-d-30">
					<div class="line"></div>
					<div class="line"></div>
					<div class="line"></div>
					<div class="line "></div>
					<div class="line"></div>
					<div class="line"></div>
					<div class="line"></div>
					<div class="line"></div>
				</div>

			</div>
		</div>
		<div v-else class="row">
			<div class="col-lg-12">
				<!-- <hr class="mg-b-30"> -->
				<!-- 
			[
			{
				"no": 32,
				"bs_platform": 0,
				"bs_platform_nm": null,
				"customer_no": null,
				"customer_name": null,
				"staff_no": null,
				"staff_name": null,
				"status": "RR",
				"status_text": "대기",
				"guest_name": "자동견적이름",
				"guest_ename": "",
				"guest_hp": "010-119-119",
				"guest_phone": "",
				"guest_email": "auto_invoice@ai.com",
				"guest_address": "",
				"guest_password": "",
				"guest_requirement": "",
				"remark_1": "",
				"remark_2": "",
				"remark_3": "",
				"res_source_cd": null,
				"res_guest_market_cd": null,
				"res_guest_class_cd": null,
				"res_rsvn_cd": null,
				"is_open": 1,
				"is_open_text": "공개",
				"checkin_local": 0,
				"checkin_local_text": "무",
				"air_schedule": "",
				"room_amount": 5000,
				"rental_car_amount": 13000,
				"golf_amount": 12000,
				"extra_amount": 0,
				"dc_amount": 0,
				"resfee": 5000,
				"resfee_kr": 125000,
				"net_amount": 0,
				"net_amount_kr": 0,
				"vat_amount": 0,
				"vat_amount_kr": null,
				"total_amount": 30000,
				"total_amount_kr": 0,
				"exchange": null,
				"payment_method": "bank",
				"useing": 1,
				"insert_date": "2023-06-29 17:41:15",
				"insert_id": 1,
				"update_date": null,
				"update_id": null,
				"is_deleted": 0,
				"insert_member": "관리자",
				"update_member": null,
				"insert_date_ymd": "2023-06-29",
				"udate_date_ymd": null,
				"useing_text": "미사용"
			}
			]
			-->
				<!-- PC 화면용 테이블 -->
				<div style="overflow-x:auto; padding-bottom: 15px;">

					<table id="list" class="table cell-border hover tx-neorg tx-13" style="table-layout: fixed" width="1131px">
						<thead>
							<tr>
								<!--0 -->
								<th data-orderable="false" data-visible="false" width="0px">#</th>
								<!--2 -->
								<th class="" data-orderable="true" data-visible="true" width="10px">견적번호</th>
								<!--1 -->
								<th class="" data-orderable="true" data-visible="true" width="10px">상태</th>
								<!--3 -->
								<th class="" data-orderable="true" data-visible="true" width="35px">예약자명</th>
								<!--4 -->
								<th class="" data-orderable="true" data-visible="true" width="35px">숙소명</th>
								<!--5 -->
								<th class="" data-orderable="true" data-visible="true" width="20px">체크인</th>
								<!--6 -->
								<th class="" data-orderable="true" data-visible="true" width="20px">체크아웃</th>
								<!--7 -->
								<th class="" data-orderable="true" data-visible="true" width="5px">박수</th>
								<!--8 -->
								<th class="" data-orderable="true" data-visible="true" width="5px">객실수</th>
								<!--9 -->

								<th class="" data-orderable="true" data-visible="true" width="30px">호실</th>

								<!--10 -->
								<th data-orderable="false" data-visible="false" width="0px">금액</th>
								<!--11-->
								<th data-orderable="true" data-visible="false" width="0px">예약일</th>
								<!--12-->
								<th data-orderable="false" data-visible="false" width="0px">useing</th>
								<!--13-->
								<th data-orderable="false" data-visible="false" width="5px">status</th>

								<!--14-->
								<th class="d-none d-sm-table-cell" data-orderable="false" data-visible="false" width="">견적내역</th>
								<!--15-->
								<th class="" data-orderable="false" data-visible="true" width="5px"></th>
							</tr>
						</thead>
						<tbody style="vertical-align:middle">
							<tr v-for="(item, idx) in list" :key="idx">
								<td align="center">{{ idx + 1 }}</td>
								<td class="" align="center">{{ item.no }}</td>
								<td class="" align="center">
									<select class="form-control w-100" v-model="item.estimate_status" @change="changes_estimate_status(item)">
										<option value="RW">등록</option>
										<option value="RR">대기</option>
										<option value="CR">확정</option>
										<option value="RC">취소</option>
										<option value="RD">삭제</option>
										<option value="NS">노쇼</option>
									</select>
								</td>
								<td class="" align="left">{{ item.guest_name }}</td>
								<td class="" align="center">
									<template v-for="(item1, idx) in item.room.list">
										<span class="pd-l-5 tx-neolt">{{ item1.room_type_nm_short }} </span><br>
									</template>
								</td>

								<td class="" align="center">
									<template v-if="item.room.total_rows > 0">
										<span class="pd-l-5 tx-neolt">{{ item.room.list[0].exp_ci_date }}</span>
									</template>
								</td>
								<td class="" align="center">
									<template v-if="item.room.total_rows > 0">
										<span class="pd-l-5 tx-neolt">{{ item.room.list[0].exp_co_date }} </span>
									</template>
								</td>
								<td class="" align="center">
									<template v-if="item.room.total_rows > 0">
										<span class="pd-l-5 tx-neolt">{{ item.room.list[0].nights }} </span>
									</template>
								</td>
								<td class="" align="center">
									<template v-if="item.room.total_rows > 0">
										<span class="pd-l-5 tx-neolt">{{ item.room.list[0].rooms }} </span>
									</template>
								</td>

								<td class="" align="center">

									<ul class="list-inline list-inline-rooms mg-b-0" style="display: inline-block;">
										<template v-for="(item1, idx) in item.room.list">
											<!-- <span class="pd-l-5 tx-neolt">{{ item1.room_type_nm_short }} </span><br> -->
											<li class="list-inline-item" v-for="room in (item1.room_number ? item1.room_number.split(',') : [])">
												<a class="btn-primary">{{ room }}</a>

											</li>
										</template>
										<!-- <li class="list-inline-item">
											<a class="btn-dark" @click="get_reservation_detail(item.estimate_no)" href="javascript:void(0);">변경</a>
										</li> -->
									</ul>

								</td>
								<td align="center">{{ $common.formatMoney(item.total_amount) }}</td>
								<td align="center">{{ item.insert_date }}</td>
								<td align="center">{{ item.useing }}</td>
								<td align="center">{{ item.estimate_status }}</td>
								<td align="center" class="d-none d-sm-table-cell">
									<div class="list-group">
										<div v-for="(item3, idx3) in item.room.list" :key="idx" href="#" class="list-group-item list-group-item-action">
											<ul class="list-inline list-inline-status mg-b-2">
												<li v-if="item.estimate_status == 'RW'" class="list-inline-item"><a class="btn-warning">{{ item3.status_text }}</a></li>
												<li v-if="item.estimate_status == 'RR'" class="list-inline-item"><a class="btn-primary">{{ item3.status_text }}</a></li>
												<li v-if="item.estimate_status == 'CR'" class="list-inline-item"><a class="btn-success">{{ item3.status_text }}</a></li>
												<li v-if="item.estimate_status == 'RC'" class="list-inline-item"><a class="btn-danger">{{ item3.status_text }}</a></li>
												<li v-if="item.estimate_status == 'RD'" class="list-inline-item"><a class="btn-danger">{{ item3.status_text }}</a></li>
												<li v-if="item.estimate_status == 'NS'" class="list-inline-item"><a class="btn-info">{{ item3.status_text }}</a></li>
											</ul>
											<div class="mg-b-0 tx-neoeb tx-14">{{ item3.guest_name }}</div>
											<template v-if="item.guest_email">
												<span class="mg-b-15">{{ item3.guest_email }}</span><br>
											</template>
											<div class="mg-t-10 mg-b-2">
												<span class="tx-14">{{ item3.room_type_nm_short }}</span>
											</div>
											<div class="mg-b-2">
												<span>{{ item3.exp_ci_date }}-{{ item3.exp_co_date }}({{ item3.nights }}박) {{ item3.rooms }}채</span><br>
											</div>
											<div v-if="item3.room_number" class="mg-b-2">
												<span class="tx-14">객실:</span>
												<ul class="list-inline list-inline-rooms mg-b-0" style="display: inline-block;">
													<li class="list-inline-item" v-for="room in (item3.room_number ? item3.room_number.split(',') : [])">
														<a class="btn-danger">{{ room }}</a>
													</li>
												</ul>
											</div>
											<span class="mg-t-15 tx-neolt tx-12">예약번호: {{ item3.estimate_no }}<br></span>
											<span class="mg-t-15 tx-neolt tx-12">등록일자: {{ item3.insert_date }}</span>
										</div>
									</div>
								</td>

								<td class="" align="center">
									<a style="color: #596882;" @click="go_detail('list',item.no, idx)" href="javascript:void(0);"><i data-feather="more-vertical"></i></a>
								</td>
							</tr>
						</tbody>
					</table>



				</div><!-- container -->
			</div><!-- container -->
		</div>



		<!-- modal start -->
		<div class="modal fade" id="modal_save" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content tx-14">
					<div class="modal-header">
						<h6 class="modal-title">선택된 생활요소를 기록하시겠습니까?</h6>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">

						<p class="modal-message mg-b-0">
						<h6 class="modal-target-title">{{selected.title}}</h6>

						<small class="modal-target-detail"> {{selected.factor_class_nm}} / 입력단위 :{{selected.factor_class_nm}} / 입력주기 : {{selected.factor_jugi_nm}}</small>
						</p>

						<div class="search_area">
							<div class="row">

								<div class="form-group col-lg-6 unit_input_date_m">
									<label for="formGroupExampleInput" class="d-block">실천일자</label>
									<input type="text" class="form-control " placeholder="실천일자 선택" id="action_datepick_m">
								</div>

								<div class="form-group col-lg-2" hidden>
									<label for="formGroupExampleInput" class="d-block">일수</label>
									<input type="text" class="form-control day_count" disabled placeholder="일수" v-model="selected.day_count">
									<input type="hidden" class="form-control " v-model="selected.startDate">
									<input type="hidden" class="form-control " v-model="selected.endDate">

								</div>
								<div :class="{
									'form-group col-lg-4 unit_type_1': selected.factor_unit_nm === '액수',
									'form-group col-lg-3 unit_type_1': selected.factor_unit_nm !== '액수',
									'd-none': ['패스', '유무'].includes(selected.factor_unit_nm)
									}">
									<label for="formGroupExampleInput" class="d-block">{{selected.factor_unit_nm}}</label>
									<div class="input-group">
										<input type="text" class="form-control text-right" placeholder="입력" :value="selected.unit || 1" @input="selected.unit = $event.target.value">
										<span v-if="selected.factor_unit_nm === '액수'" class="input-group-text" style="font-size: 12px;">원</span>
									</div>
								</div>
							</div>
							<div class="row">

								<div class="form-group col-lg-12">
									<label for="formGroupExampleInput" class="d-block">메모</label>
									<input type="text" class="form-control " placeholder="메모" v-model="selected.bigo_1">
								</div>


							</div>
						</div>



					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary tx-13" @click="achive_merit()">저장하기</button>

					</div>
				</div>
			</div>
		</div>
	</div><!-- row -->


	<script>
		// <!-- vue unyict lib -->
		$.getScript("/assets/js/unyict/common-lib_v1_6.js", function() {
			//로드 완료 후 로컬 js 로드.
			$.getScript("/views/dfadmin/basic/estimate/estimatelist/js/list.js");
			//$.getScript("/views/dfadmin/basic/estimate/estimatelist/js/write.js");
		});
		$(document).ready(function() {
			// 여기에 다른 초기화 .
		});
	</script>