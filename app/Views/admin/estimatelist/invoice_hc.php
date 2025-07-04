<div id="app" style="width: 955px; margin: auto;">
	<hr class="mg-t-10 mg-b-5">

	<div class="d-flex flex-row justify-content-between">
		<div class="align-self-center">
			<h5 class="mg-t-10" style="font-family:'NanumSquareNeo BD'">
				[{{$app_estimate.lib_estimate.status_text}}] {{$app_estimate.lib_estimate.guest_name}}님
			</h5>
		</div>
		<div class="align-self-center">
			<a class="btn btn-xs btn-outline-success" href="javascript:;" onclick="exportJPG()"> <i data-feather="printer" class="mg-r-5"></i>JPG</a>
			<a class="btn btn-xs btn-outline-danger" href="javascript:;" onclick="exportPDF()"> <i data-feather="file-text" class="mg-r-5"></i>PDF</a>
		</div>
	</div>
	<hr class="mg-t-0 mg-b-15">
	<div v-if="$common.is_loding_list_1" class='pd-10'>
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
	<div v-else id="invoice" class='pd-10'>
		<!-- logo header area start  -->
		<div class="d-flex mb-0">
			<div class="pd-0 flex-fill">
				<div class="carousel slide" data-ride="carousel">
					<div class="carousel-inner">
						<div class="carousel-item active">
							<!-- brand-logo start -->
							<img :src="'/' + $app_estimate.lib_estimate.partner_info.image1" height="120" class="d-block mx-auto ">
							<!-- brand-logo end -->
						</div>
						<!-- more items goes here -->
					</div>
				</div>
			</div>
		</div>
		<div class="d-flex mb-0	">
			<div class="pd-0 flex-fill">
				<ul class="list-unstyled lh-7 mb-2">

					<li class="d-flex justify-content-center tx-medium">
						<span>하이클래스(코리아타운점) </span>

					</li>
				</ul>
			</div>

		</div>
		<!-- logo header area end -->
		<div class="d-flex mb-0	">
			<div class="pd-10 bg-gray-100 flex-fill">
				<ul class="list-unstyled lh-7 mb-0">
					<!-- <li class="d-flex justify-content-between">
						<span>견적번호</span>
						<span>{{$app_estimate.lib_estimate.no}} </span>
					</li>
					<li class="d-flex justify-content-between">
						<span>견적고객</span>
						<span>{{$app_estimate.lib_estimate.guest_name}} </span>
					</li>
					<li class="d-flex justify-content-between">
						<span>견적일자</span>
						<span>{{$app_estimate.lib_estimate.insert_date_ymd}} </span>
					</li> -->
					<li class="d-flex justify-content-center tx-medium">
						<!-- <span>투숙일정</span> -->
						<span>{{$app_estimate.lib_estimate.room_estimate.res_info.exp_ci_date}} - {{$app_estimate.lib_estimate.room_estimate.res_info.exp_co_date}}</span>
						<span> &middot; {{$app_estimate.lib_estimate.room_estimate.res_info.nights}}박</span>
						<span> &middot; {{$app_estimate.lib_estimate.guests}}인</span>
						<span> &middot; {{$app_estimate.lib_estimate.guest_name}}님</span>
						<span> &middot; {{$app_estimate.lib_estimate.insert_date_ymd}}</span>
					</li>
				</ul>
			</div>

		</div>

		<div class="table-responsive mg-t-0">
			<table class="table table-invoice bd-b tx-nowrap mg-b-0">
				<thead>
					<tr class="bg-gray-100" style="border-top: 2px solid #99a0ae; border-bottom: 1px solid #99a0ae;">
						<th class=" tx-center bd-r" style="width:238px !important;">구분</th>
						<th class="wd-60p tx-center bd-r" colspan='2'>내용</th>
						<!-- <th class="wd-10p tx-center bd-r">일수/인원</th> -->
						<th v-if="estimate.is_open === '1'" class=" tx-center">금액(페소)</th>
					</tr>
				</thead>
				<tbody>

					<!-- 숙박 -->
					<template v-if="$app_estimate.lib_estimate.room_estimate.room_checked.length > 0">
						<tr v-for="(item, index) in $app_estimate.lib_estimate.room_estimate.room_checked">
							<td class=" tx-nowrap tx-center bd-t-0-f bd-r">
								<span class="tx-15 tx-bold">숙박 </span>
								<!-- <small class="form-text text-danger">***체크인 30일 전까지 예약 취소/환불 가능</small> -->
							</td>
							<td class=" tx-medium bd-t-0-f bd-r tx-15" colspan='2'>
								<!-- > {{item.room_type_nm}} / {{$common.formatMoney(item.resfee)}} * {{item.nights}}박 -->
								> 하이클래스 코리아타운 / <template v-if="estimate.is_open === '1'">{{$common.formatMoney(item.resfee)}} * </template> {{item.nights}}박
								<small v-if="item.accoms_type_nm !== '호텔' " class="form-text">(익일 새벽 체크인, 1룸&1인 조식 포함)</small>
							</td>
							<!-- <td class="tx-right tx-medium bd-t-0-f bd-r tx-15">{{$common.formatMoney(item.resfee)}}</td>
							<td class="tx-center tx-medium bd-t-0-f bd-r tx-15">{{item.nights}}</td> -->
							<td v-if="estimate.is_open === '1'" class="tx-right tx-medium bd-t-0-f tx-15">{{$common.formatMoney(item.total_amount)}}</td>
						</tr>
					</template>

					<!-- 차량 -->
					<!-- 차량 -->
					<template v-if="groupedEstimates.length > 0">

						<tr v-for="(item, index) in groupedEstimates">
							<td class="tx-nowrap tx-center bd-r" v-if="index === 0" :rowspan="groupedEstimates.length">
								<span class="tx-15 tx-bold">차량 </span>
							</td>
							<td class=" tx-medium bd-r tx-15" colspan='2'>
								> {{item.key_car_name}}
								<small class="form-text">{{item.key_description}}</small>
								<template v-for="(item_detail, index_detail) in item.items">
									<small class="form-text">{{item_detail.remark_1}} "{{item_detail.rental_date}}" {{item_detail.rental_counts}}대</small>
								</template>


							</td>
							<!-- <td class="tx-right tx-medium bd-r tx-15">{{$common.formatMoney(item.key_cost_per_night)}}</td>
							<td class="tx-center tx-medium bd-r tx-15">{{item.key_sum_rental_days}}
								<template v-if="item.key_sum_rental_counts > 1">/{{item.key_sum_rental_counts}}</template>
							</td> -->
							<td v-if="estimate.is_open === '1'" class="tx-right tx-medium tx-15">{{$common.formatMoney(item.key_sum_rental_amount)}}</td>
						</tr>
					</template>

					<!-- 골프 -->
					<template v-if="$app_estimate.lib_estimate.golf_estimate.length > 0">
						<tr v-for="(item, index) in $app_estimate.lib_estimate.golf_estimate">
							<td class="tx-nowrap tx-center bd-r" v-if="index === 0" :rowspan="$app_estimate.lib_estimate.golf_estimate.length + 1">
								<span class="tx-15 tx-bold">골프 </span>
								<small class="form-text text-danger">( 카트/캐디/보험/의무사용 포함 )</small>
								<small class="form-text text-danger">*플레이 인원 홀수인 경우, 싱글 카트 비용 추가</small>
							</td>
							<td class=" tx-medium bd-r tx-15" colspan='2'>
								> {{item.golf_date}} {{item.golf_name}} / <template v-if="estimate.is_open === '1'"> {{$common.formatMoney(item.greenfee)}}* </template>{{item.golf_people}}명
							</td>

							<td v-if="estimate.is_open === '1' && index === 0" :rowspan="$app_estimate.lib_estimate.golf_estimate.length + 1" class="tx-right tx-medium tx-15">{{$common.formatMoney($app_estimate.lib_estimate.golf_amount)}}</td>
						</tr>
						<tr>
							<td class=" tx-medium bd-r tx-15" colspan='2'>
								> 카트/캐디/보험/의무사용 포함
							</td>

						</tr>
					</template>
					<!-- 기타여행 -->
					<template v-if="$app_estimate.lib_estimate.item_estimate.length > 0">

						<template v-for="(item, index) in $app_estimate.lib_estimate.item_estimate">
							<!-- 첫 번째 행 -->
							<tr>
								<td class="tx-nowrap tx-center bd-r" v-if="index === 0" :rowspan="getRowSpan($app_estimate.lib_estimate.item_estimate)">
									<span class="tx-15 tx-bold">기타 </span>
								</td>
								<td class=" tx-medium bd-r tx-15" colspan='2'> > {{item.date}} {{item.name}}
									<template v-if="item.cit_bigo1">
										({{removeHtmlTags(item.cit_bigo1)}})
									</template>
									/<template v-if="estimate.is_open === '1'"> {{$common.formatMoney(item.cit_regular_price)}} * </template>{{item.people}}명
								</td>

								<td v-if="estimate.is_open === '1' && index === 0" :rowspan="getRowSpan($app_estimate.lib_estimate.item_estimate)" class="tx-right tx-medium tx-15">{{$common.formatMoney($app_estimate.lib_estimate.item_amount)}}</td>

							</tr>
							<!-- 추가된 부분: item.estimate_item_detail이 존재할 경우 -->
							<tr v-for="(item_detail, index_detail) in item.estimate_item_detail">
								<td class=" tx-medium bd-r tx-15" colspan='2'> > {{item.date}} {{item_detail.detail_name}} / {{$common.formatMoney(item_detail.cde_price)}} * {{item_detail.detail_count}}명</td>

							</tr>
						</template>

					</template>
			</table>
			<table class="table table-invoice bd-b tx-nowrap mg-b-0">
				<!-- 정상가격 -->
				<template v-if="$app_estimate.lib_estimate.dc_amount > 0 ">

					<tr style="border-top: 3px double #99a0ae;">
						<td style="width:238px !important;" class="tx-nowrap tx-center bd-r" rowspan="2">
							<span class="tx-15 tx-bold">정상가격 </span>
						</td>
						<td class=" tx-center tx-medium bd-r"> 1인 기준</td>
						<td class="tx-center tx-medium bd-r"> 합계(한화 약)</td>
						<td class="tx-center tx-medium ">합계</td>
					</tr>
					<tr style=" border-bottom: 3px double #99a0ae;">
						<td class=" tx-center tx-medium bd-r">
							<div class="d-flex justify-content-between tx-medium text-danger tx-16">
								<span>₩</span>
								<span v-if="$app_estimate.lib_estimate.pre_amount_kr > 0">{{ $common.formatMoney(Math.round(($app_estimate.lib_estimate.pre_amount_kr / $app_estimate.lib_estimate.guests))) }}</span>
								<span v-else>{{ $common.formatMoney(Math.round(($app_estimate.lib_estimate.pre_amount / $app_estimate.lib_estimate.guests) * exchange)) }}</span>

							</div>
						</td>
						<td class="tx-center tx-medium bd-r">
							<div class="d-flex justify-content-between tx-medium text-danger tx-16">
								<span>₩</span>
								<span v-if="$app_estimate.lib_estimate.pre_amount_kr > 0">{{$common.formatMoney(($app_estimate.lib_estimate.pre_amount_kr))}}</span>
								<span v-else>{{$common.formatMoney(($app_estimate.lib_estimate.pre_amount * exchange))}}</span>
							</div>
						</td>
						<td class="tx-center tx-medium ">
							<div class="d-flex justify-content-between tx-medium text-danger tx-16">
								<span>PhP</span>
								<span>{{$common.formatMoney($app_estimate.lib_estimate.pre_amount)}}</span>
							</div>
						</td>
					</tr>
				</template>
				<!-- !정상가격 -->
				<!-- 할인적용 -->
				<template v-if="$app_estimate.lib_estimate.dc_amount > 0 ">
					<tr style="border-top: 3px double #99a0ae;">
						<td class="tx-nowrap tx-center ">
							<span class="tx-15 tx-bold">할인적용 </span>
						</td>
						<td class="tx-nowrap tx-center" colspan="2">
							<span class="tx-15 tx-bold">&nbsp; </span>
						</td>

						<td class=" tx-center tx-medium">
							<div class="d-flex justify-content-between tx-medium text-danger tx-16">
								<span>PhP</span>
								<span>- {{ $common.formatMoney($app_estimate.lib_estimate.dc_amount) }}</span>

							</div>

						</td>
					</tr>

				</template>
				<!-- !할인적용 -->
				<!-- 비용 -->
				<tr style="border-top: 3px double #99a0ae;">
					<td class="tx-nowrap tx-center bd-r" style="width:238px !important;" rowspan="2">
						<span class="tx-15 tx-bold">최종가격 </span>
					</td>
					<td class=" tx-center tx-medium bd-r"> 1인 기준</td>
					<td class="tx-center tx-medium bd-r"> 합계(한화 약)</td>
					<td class="tx-center tx-medium ">합계(페소)</td>
				</tr>
				<tr style=" border-bottom: 3px double #99a0ae;">
					<td class=" tx-center tx-medium bd-r">
						<div class="d-flex justify-content-between tx-medium text-danger tx-16">
							<span>₩</span>
							<span v-if="$app_estimate.lib_estimate.total_amount_kr > 0">{{ $common.formatMoney(Math.round(($app_estimate.lib_estimate.total_amount_kr / $app_estimate.lib_estimate.guests))) }}</span>
							<span v-else>{{ $common.formatMoney(Math.round(($app_estimate.lib_estimate.total_amount / $app_estimate.lib_estimate.guests) * exchange)) }}</span>

						</div>
					</td>
					<td class="tx-center tx-medium bd-r">
						<div class="d-flex justify-content-between tx-medium text-danger tx-16">
							<span>₩</span>
							<span v-if="$app_estimate.lib_estimate.total_amount_kr > 0">{{$common.formatMoney($app_estimate.lib_estimate.total_amount_kr)}}</span>
							<span v-else>{{$common.formatMoney(($app_estimate.lib_estimate.total_amount * exchange))}}</span>
						</div>
					</td>
					<td class="tx-center tx-medium ">
						<div class="d-flex justify-content-between tx-medium text-danger tx-16">
							<span>PhP</span>
							<span>{{$common.formatMoney($app_estimate.lib_estimate.total_amount)}}</span>
						</div>
					</td>
				</tr>
			</table>
			<table class="table table-invoice bd-b tx-nowrap">
				<!-- 예약금 1박 -->
				<tr class="bg-gray-100">
					<td style="width:238px !important;" class="tx-nowrap tx-center tx-medium  bd-r">
						<span class="tx-14  ">예약금</span>
					</td>
					<td class=" tx-left tx-medium " colspan="5">
						<template v-if="estimate.is_open === '1'">
							<span class="form-text ">
								{{resfee_type_nm_text}}
								<template v-if="$app_estimate.lib_estimate.room_estimate.room_resfee_kr > 0">{{$common.formatMoney($app_estimate.lib_estimate.room_estimate.room_resfee_kr)}}</template>
								<template v-else>{{$common.formatMoney($app_estimate.lib_estimate.room_estimate.room_resfee * exchange)}}</template>원 ({{$app_estimate.lib_estimate.room_estimate.room_resfee}} 페소)을 아래 계좌에 입금해 주시면 예약이 확정됩니다.
							</span>
							<span class="form-text">
								<template v-if="$app_estimate.lib_estimate.golf_amount > 0">
									골프는 골프요금
									<template v-if="$app_estimate.lib_estimate.golf_amount_kr > 0">
										{{$common.formatMoney($app_estimate.lib_estimate.golf_amount_kr)}}
									</template>
									<template v-else>{{$common.formatMoney($app_estimate.lib_estimate.golf_amount * exchange)}}</template>원 ({{$common.formatMoney($app_estimate.lib_estimate.golf_amount)}} 페소) 입금 후, 예약 신청 진행됩니다.
								</template>
							</span>
						</template>
						<template v-else>
							예약금(1인 기준 금액) 입금 후, 예약 확정 <br>
							예약금을 제외한 잔금은, 현지에서 체크아웃 전에 현지 한국인 매니저님께 페소 결제
						</template>

						<span class="form-text" class="tx-15 tx-medium">***국민은행 정민석(인앤인) 165801-04-308286</span>
					</td>
				</tr>

				<!-- 잔금 -->
				<tr class="bg-gray-100">
					<td class="tx-nowrap tx-center tx-medium  bd-r">
						<span class="tx-15">비고 </span>
					</td>
					<td class=" tx-left tx-medium  lh-12" colspan="5">
						<span class="form-text ">> 예약금 입금 전, 예약가능 여부 재확인이 필요하므로 사전 연락주시기 바랍니다.</span>
						<span class="form-text ">> 3박 이상 예약시, 레이트 체크아웃 무료로 가능하나(새벽 체크인에 한함), <br>&nbsp;&nbsp;&nbsp; 다음 고객 입실 시간에 따라 불가할 수 있습니다.</span>
						<span class="form-text ">> 체크인 30일 전까지 예약 취소/환불 가능합니다.</span>
						<span class="form-text ">> 예약금을 제외한 나머지 잔금은 현지에서 체크아웃 전에, 현지매니저님께 페소로 지불해주시면 됩니다.</span>
						

				

					</td>

				</tr>

				</tbody>
			</table>
		</div>

		<div class="row justify-content-between">
			<div class="col-sm-12 col-lg-12 order-2 order-sm-0 mg-t-40 mg-sm-t-0">
				<label class="tx-sans tx-uppercase tx-10 tx-medium tx-spacing-1 tx-color-03">Notes</label>
				<p class="tx-danger" v-html="$app_estimate.lib_estimate.remark_1.split('\n').join('<br/>')"> </p>
			</div>
			<!-- col -->
			<!-- <div class="col-sm-6 col-lg-4 order-1 order-sm-0"> -->
			<!-- <ul class="list-unstyled lh-7 pd-r-10">
				<li class="d-flex justify-content-between">
					<span>Sub-Total</span>
					<span>$5,750.00</span>
				</li>
				<li class="d-flex justify-content-between">
					<span>Tax (5%)</span>
					<span>$287.50</span>
				</li>
				<li class="d-flex justify-content-between">
					<span>Discount</span>
					<span>-$50.00</span>
				</li>
				<li class="d-flex justify-content-between">
					<strong>Total Due</strong>
					<strong>$5,987.50</strong>
				</li>
			</ul> -->

			<!-- <button class="btn btn-block btn-primary">Pay Now</button> -->
			<!-- </div> --><!-- col -->
		</div>


	</div>
	<!-- <button onclick="exportPDF()">Export to PDF</button>
	<button onclick="exportJPG()">Export to JPG</button> -->
	<!-- modal start -->
	<div class="modal fade" id="modal_save" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content tx-14">
				<div class="modal-header">
					<h6 class="modal-title" id="exampleModalLabel2">AutoInvoice System</h6>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p class="mg-b-0">저장하시겠습니까? </p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary tx-13" @click="save_estimate();">저장하기</button>

				</div>
			</div>
		</div>
	</div>



	<div class="modal fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content tx-14">
				<div class="modal-header">
					<h6 class="modal-title" id="exampleModalLabel2">AutoInvoice system</h6>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p class="mg-b-0">삭제하시겠습니까?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-danger tx-13" @click="delete_estimate();">삭제하기</button>
				</div>
			</div>
		</div>
	</div>


	<!-- select room Modal -->
	<div class="modal fade" id="roomSelectModal" tabindex="-1" role="dialog" aria-labelledby="roomSelectModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h6 class="modal-title" id="exampleModalLabel2">AutoInvoice system</h6>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<!-- search area start-->
					<div class="row search_area mg-l-1 mg-r-1">
						<div class="form-group col-lg-4">
							<label for="formGroupExampleInput" class="d-block ">숙박일정</label>
							<input type="text" id="exp_date" name="exp_date" class="form-control hasDatepicker tx-center" placeholder="숙박일정">
						</div>

						<div class="form-group col-lg-2 col-sm-6 ">
							<label for="commoncode_1_select" class="d-block">풀빌라</label>
							<select class="custom-select tx-center" id="commoncode_1_select" v-model="search.sel_rooms">
								<option value="1">1채</option>
								<option value="2">2채</option>
								<option value="3">3채</option>
								<option value="4">4채</option>
								<option value="5">5채</option>
							</select>
						</div>
						<div class="form-group col-lg-2 col-sm-6 ">
							<label for="commoncode_2_select" class="d-block">인원</label>
							<select class="custom-select tx-center" id="commoncode_2_select" v-model="search.sel_guests">
								<option value="1">1인</option>
								<option value="2">2인</option>
								<option value="3">3인</option>
								<option value="4" selected>4인</option>
								<option value="5">5인</option>
								<option value="6">6인</option>
								<option value="7">7인</option>
								<option value="8">8인</option>
								<option value="9">9인</option>
								<option value="10">10인</option>
							</select>
						</div>

						<div class="form-group col-lg-4">
							<label for="formGroupExampleInput" class="d-block">&nbsp</label>
							<button type="button" class="btn btn-primary btn-block" @click="getavailableroom()">숙소 검색하기</button>
						</div>
					</div>
					<template v-if="searched_rooms.count_total < 1">
						<div v-if="is_room_searched" class="alert alert-danger" role="alert">
							해당 조건으로 예약 가능한 객실이 없습니다.
						</div>
						<div v-else class="alert alert-info	" role="alert">
							원하시는 여행 일정을 검색해주세요.
						</div>
					</template>
					<template v-else>
						<div class="row">
							<div class="col-lg-12 table-responsive">

								<table id="ab_room_list" class="table table-sm" width="100%">
									<thead>
										<tr align="center">
											<th width="5%">#</th>
											<th width="">구분</th>
											<th width="">객실</th>
											<th width="">투숙일정</th>
											<th width="">총요금</th>
											<th width="180px"></th>
										</tr>
									</thead>

									<tbody>
										<tr v-for="(item, idx) in searched_rooms.result_ab_room" :class="[(checkedRes.includes(item.room_type_no) ? 'active_row' : ''), $app_estimate.getColor(item) ]" :style="{ 'background-color': $app_estimate.getColor_background(item) }">
											<td align="center">{{ idx + 1 }}</td>
											<td align="center">{{ item.accoms_nm }}</td>
											<td align="center">{{ item.type_name }}</td>
											<td align="center">{{ item.check_in }}-{{ item.check_out }}({{ item.req_nights }}박),{{ item.req_rooms }}채</td>
											<td align="center">{{$common.formatMoney(item.charge_amount_total)}} 페소({{$common.formatMoney(item.charge_amount_total_kr)}}원)</td>

											<td align="center">

												<div class="tx-center flex-fill align-self-center">
													<div v-if="item.available_rooms === 0" class="btn-group align-top">
														<button type="button" data-toggle="modal" data-target="#user-form-modal" class="btn btn-danger badge">예약불가</button>
													</div>
													<div v-else class="custom-control custom-checkbox">
														<input hidden type="checkbox" :id="'customCheck' + idx" :value="item.room_type_no" v-model="checkedRes" @change="selectRoom">
														<label class="btn btn-sm btn-outline-primary" :for="'customCheck' + idx">{{ checkedRes.includes(item.room_type_no) ? '선택 해제' : '선택' }}</label>
													</div>
												</div>

											</td>
										</tr>

									</tbody>
								</table>
							</div>수
							<!-- search area end -->
							<!-- 	<img v-if="selectedImage" :src="selectedImage.source" class="img-fluid" :alt="selectedImage.alt"> -->
						</div>
					</template>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
						<!-- <button type="button" class="btn btn-primary tx-13" @click="select_room();">선택완료</button> -->
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- model end -->
</div><!-- row -->

<script>
	// <!-- vue unyict lib -->
	$.getScript("/assets/js/unyict/common-lib_v1_6.js", function() {
		$.getScript("/assets/js/unyict/estimate-lib.js", function() {
			//로드 완료 후 로컬 js 로드.
			$.getScript("/views/dfadmin/basic/estimate/estimatelist/js/invoice.js");
		});
	});
	$(document).ready(function() {
		// 여기에 다른 초기화 .
	});
</script>



<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>

<script>
	function exportPDF() {
		let filename = '견적서_' + view_data.data.guest_name + '님(' + view_data.data.room_estimate.res_info.exp_ci_date + "~" + view_data.data.room_estimate.res_info.exp_ci_date + ").pdf"; // replace this with the actual variable
		let element = document.getElementById('invoice');
		let opt = {
			margin: 1,
			filename: filename,
			image: {
				type: 'jpeg',
				quality: 0.98
			},
			html2canvas: {
				scale: 1
			},
			jsPDF: {
				unit: 'mm',
				format: 'a4',
				orientation: 'portrait'
				//	orientation: 'landscape' // Changed to landscape format
			} // Changed to A4 format
		};
		html2pdf().from(element).set(opt).save();
	}

	function exportJPG() {
		let filename = '견적서_' + view_data.data.guest_name + '님(' + view_data.data.room_estimate.res_info.exp_ci_date + "~" + view_data.data.room_estimate.res_info.exp_ci_date + ").jpg"; // replace this with the actual variable
		let element = document.getElementById('invoice');
		html2canvas(element).then((canvas) => {
			let imgData = canvas.toDataURL('image/jpeg');
			let link = document.createElement('a');
			link.href = imgData;
			link.download = filename;
			link.click();
		});
	}
</script>