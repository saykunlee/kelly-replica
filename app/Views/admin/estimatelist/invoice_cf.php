<div id="app" style="width: 814px; margin: auto;">
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

	<div v-if="$common.is_loding_list_1">
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
	<div v-else id="invoice" class='estimate_a4 pd-r-15 pd-l-15 pd-t-20'>

		<!-- 헤더부분 시작 -->
		<div class="row mg-b-30">
			<!-- 견적결의서 -->
			<div class="col-6 d-flex justify-content-start align-items-center">
				<div>

					<h1 class="tx-neohv " style="font-size: 3rem;">견적결의서</h1>
					<span class="tx-neobd tx-gray-700" style="font-size: 1.05rem;">견적일자: {{$common.formattedDate_kor($app_estimate.lib_estimate.insert_date_ymd)}}</span>
				</div>

			</div>
			<!-- 견적결의서 -->
			<!-- 결재라인 -->
			<div class="col-6 d-flex justify-content-end">
				<table class="table-invoice-appline tx-14 tx-neorg tx-black bd-x bd-y bd-color-appline">
					<tr>
						<th rowspan="2" class="tx-nowrap tx-center th-width bd-r bd-color-appline">
							<span class="">결<br>재</span>
						</th>
						<th class="tx-center bd-r bd-color-appline">총무</th>
						<th class="tx-center bd-r bd-color-appline">임원</th>
						<th class="tx-center bd-r bd-color-appline">부회장</th>
						<th class="tx-center bd-color-appline">회장</th>
					</tr>
					<tr class="">
						<td class="tx-center td-height-width bd-t bd-r bd-color-appline"></td>
						<td class="tx-center td-height-width bd-t bd-r bd-color-appline"></td>
						<td class="tx-center td-height-width bd-t bd-r bd-color-appline"></td>
						<td class="tx-center td-height-width bd-t bd-color-appline"></td>
					</tr>
				</table>
			</div>
			<!-- !결재라인 -->
		</div>
		<!-- 헤더부분 끝 -->



		<!-- 고객정보 -->
		<div class="row">
			<div class="col-3 d-flex justify-content-start align-items-center">
				<!-- 로고 -->
				<table class="table table-invoice-appline tx-14 tx-neorg tx-black" style="width:100%">
					<tr class="">
						<td class="tx-nowrap tx-center" style="height:150px; border-top:0 !important;">
							<div class="carousel slide" data-ride="carousel">
								<div class="carousel-inner">
									<div class="carousel-item active">
										<!-- brand-logo start -->
										<img :src="'/' + $app_estimate.lib_estimate.partner_info.image1" height="90" class="d-block mx-auto">
										<!-- brand-logo end -->
									</div>
									<!-- more items goes here -->
								</div>
							</div>
							<!-- logo area end -->
						</td>

					</tr>
				</table>


			</div>
			<!-- 로고 -->
			<!-- 기본정보 -->
			<div class="col-9 d-flex justify-content-start">
				<table class="table table-invoice-appline tx-14 tx-neorg tx-black bd-y bd-color-appline" style="width:100%">

					<tr>
						<th class="tx-nowrap tx-center td-line-height-2 bd-r bd-l bd-color-appline table-invoice-appline-bg" colspan="4">
							클락필드에서 결재를 요청합니다. 결재를 하시면 클락필드에서 홀인원/잭팟이 터집니다.
						</th>

					</tr>
					<tr class="">
						<th class="tx-nowrap tx-center td-line-height-2 bd-r bd-l bd-color-appline wd-80 table-invoice-appline-bg">
							<span class="">성함 </span>
						</th>
						<td class=" tx-center  td-line-height-2 bd-r  bd-color-appline wd-250"> {{$app_estimate.lib_estimate.guest_name}}</td>
						<th class="tx-nowrap tx-center td-line-height-2 bd-r  bd-color-appline wd-80 table-invoice-appline-bg">
							<span class="">숙소명 </span>
						</th>
						<td class=" tx-center  td-line-height-2 bd-r  bd-color-appline"> {{$app_estimate.lib_estimate.room_estimate.room_checked[0].accoms_nm}}</td>
					</tr>

					<tr>
						<th class="tx-nowrap tx-center td-line-height-2 bd-r bd-l bd-color-appline table-invoice-appline-bg">
							<span class="">인원 </span>
						</th>
						<td class=" tx-center  td-line-height-2 bd-r bd-color-appline"> {{$app_estimate.lib_estimate.guests}}인</td>
						<th class="tx-nowrap tx-center   td-line-height-2 bd-r bd-color-appline table-invoice-appline-bg">
							<span class=" ">룸타입 </span>
						</th>
						<td class=" tx-center  td-line-height-2 bd-r bd-color-appline"> {{$app_estimate.lib_estimate.room_estimate.room_checked[0].room_type_nm_short}}</td>
					</tr>

					<tr>
						<th class="tx-nowrap tx-center td-line-height-2 bd-r bd-l bd-color-appline table-invoice-appline-bg">
							<span class="">기간 </span>
						</th>
						<td class="tx-center td-line-height-2 bd-r bd-color-appline">
							{{$app_estimate.lib_estimate.room_estimate.res_info.exp_ci_date}} - {{$app_estimate.lib_estimate.room_estimate.res_info.exp_co_date}}
							( {{$app_estimate.lib_estimate.room_estimate.res_info.nights}}박 )
						</td>
						<th class="tx-nowrap tx-center td-line-height-2 bd-r bd-color-appline table-invoice-appline-bg">
							<span class="">입국공항 </span>
						</th>
						<td class=" tx-center td-line-height-2 bd-r bd-color-appline"> {{$app_estimate.lib_estimate.air_schedule}}</td>
					</tr>
				</table>
			</div>
			<!-- 기본정보 -->
		</div>
		<!-- !고객정보 -->

		<!-- 포함사항 -->

		<table class="table table-invoice-appline tx-14 tx-neorg tx-black bd-y bd-color-appline" style="width:100%">

			<tbody>
				<tr class="">
					<th class="tx-nowrap tx-center td-line-height-2 bd-r bd-l bd-color-appline wd-100 table-invoice-appline-bg" style="height:100px;">
						<span class="">포함사항 </span>
					</th>
					<td class=" tx-center td-line-height-2 bd-r bd-color-appline"> </td>
					<th class="tx-nowrap tx-center td-line-height-2 bd-r bd-color-appline wd-100 table-invoice-appline-bg">
						<span class="">불포함사항 </span>
					</th>
					<td class=" tx-center td-line-height-2 bd-r bd-color-appline"> </td>
				</tr>
				<tr class="">
					<th class="tx-nowrap tx-center td-line-height-2 bd-r bd-l bd-color-appline wd-80 table-invoice-appline-bg" style="height:70px;">
						<span class="">기타제공 <br> 가능 서비스 </span>
					</th>

					<td class="tx-nowrap tx-center td-line-height-2 bd-r bd-color-appline" colspan="3">

					</td>

				</tr>



			</tbody>

		</table>

		<!-- !포함사항 -->

		<!-- 견적내용 -->

		<table class="table table-invoice-appline-detail tx-14 tx-neorg tx-black bd-y bd-color-appline" style="width:100%">
			<thead>
				<tr>
					<th class="tx-center-vertical bd-r bd-l " style="width: 190px;">항목</th>
					<th class="tx-center-vertical bd-r " style="width: 500px;">내용</th>
					<th v-if="estimate.is_open === '1'" class="tx-center-vertical bd-r" style="width: 125px;">기본요금 <br>(페소)</th>
					<th class="wd-100 tx-center-vertical bd-r" style="width: 110px;">일수/인원</th>
					<th v-if="estimate.is_open === '1'" class="tx-center-vertical bd-r" style="width:190px">개별항목 <br>총 요금(페소)</th>
				</tr>
			</thead>
			<tbody>
				<!-- 숙박 -->
				<template v-if="$app_estimate.lib_estimate.room_estimate.room_checked.length > 0">
					<tr v-for="(item, index) in $app_estimate.lib_estimate.room_estimate.room_checked">
						<td class="  tx-center  bd-r bd-l">
							<span class=" tx-neobd">숙박 </span>
							<br>
							<small class="form-text tx-neobd text-danger">체크인 30일 전까지 예약 취소/환불 가능</small>
						</td>
						<td class="   bd-r ">
							{{item.room_type_nm}}
							<small v-if="item.accoms_type_nm !== '호텔' " class="form-text">(익일 새벽 체크인, 1룸&1인 조식 포함)</small>
						</td>
						<td v-if="estimate.is_open === '1'" class="tx-right   bd-r ">{{$common.formatMoney($app_estimate.get_single_room_charge)}}</td>
						<td class="tx-center   bd-r ">{{item.nights}}</td>
						<td v-if="estimate.is_open === '1'" class="tx-right    bd-r">{{$common.formatMoney(item.total_amount)}}

						</td>
					</tr>
				</template>
				<!-- 차량 -->
				<template v-if="groupedEstimates.length > 0">

					<tr v-for="(item, index) in groupedEstimates">
						<td class="tx-center bd-r bd-l" v-if="index === 0" :rowspan="groupedEstimates.length">
							<span class="tx-neobd ">차량 </span>
						</td>
						<td class="  bd-r ">
							{{item.key_car_name}}
							<small class="form-text">{{item.key_description}}</small>
							<template v-for="(item_detail, index_detail) in item.items">
								<small class="form-text">{{item_detail.remark_1}} "{{item_detail.rental_date}}" {{item_detail.rental_counts}}대</small>
							</template>


						</td>
						<td v-if="estimate.is_open === '1'" class="tx-right  bd-r ">{{$common.formatMoney(item.key_cost_per_night)}}</td>
						<td class="tx-center  bd-r ">{{item.key_sum_rental_days}}
							<!-- <template v-if="item.key_sum_rental_counts > 1">/{{item.key_sum_rental_counts}}</template> -->
						</td>
						<td v-if="estimate.is_open === '1'" class="tx-right  bd-r">{{$common.formatMoney(item.key_sum_rental_amount)}}</td>
					</tr>
				</template>
				<!-- !차량 -->

				<!-- 골프 -->
				<template v-if="$app_estimate.lib_estimate.golf_estimate.length > 0">
					<tr v-for="(item, index) in $app_estimate.lib_estimate.golf_estimate">
						<td class="tx-center bd-r bd-l" v-if="index === 0" :rowspan="$app_estimate.lib_estimate.golf_estimate.length">
							<span class="tx-neobd">골프 </span>
							<small class="form-text text-danger">( 카트/캐디/보험/의무사용 포함 )</small>
							<small class="form-text text-danger">*플레이 인원 홀수인 경우, 싱글 카트 비용 추가</small>
						</td>
						<td class="  bd-r "> {{item.golf_date}} {{item.golf_name}} <!-- {{item.golf_course}} --> </td>
						<td v-if="estimate.is_open === '1'" class="tx-right  bd-r ">{{$common.formatMoney(item.greenfee)}}</td>
						<td class="tx-center  bd-r ">{{item.golf_people}}</td>
						<td v-if="estimate.is_open === '1'" class="tx-right  bd-r">{{$common.formatMoney(item.golf_amount)}}</td>
					</tr>
				</template>
				<!-- !골프 -->
				<!-- 기타여행 -->
				<template v-if="$app_estimate.lib_estimate.item_estimate.length > 0">
					<template v-for="(item, index) in $app_estimate.lib_estimate.item_estimate">
						<!-- 첫 번째 행 -->
						<tr>
							<td class="tx-nowrap tx-center bd-r bd-l" v-if="index === 0" :rowspan="getRowSpan($app_estimate.lib_estimate.item_estimate)">
								<span class=" tx-neobd">기타</span>
							</td>
							<td class="  bd-r "> {{item.date}} {{item.name}}
								<template v-if="item.cit_bigo1">
									({{removeHtmlTags(item.cit_bigo1)}})
								</template>
							</td>
							<td v-if="estimate.is_open === '1'" class="tx-right  bd-r ">{{$common.formatMoney(item.cit_regular_price)}}</td>
							<!-- <td class="tx-right  bd-r ">{{$common.formatMoney(item.item_price)}}</td> -->
							<td class="tx-center  bd-r ">{{item.people}}</td>
							<!-- <td class="tx-right  ">{{$common.formatMoney(item.item_amount)}}</td> -->
							<td v-if="estimate.is_open === '1'" class="tx-right  ">{{$common.formatMoney(item.item_amount_without_detail)}}</td>
						</tr>
						<!-- 추가된 부분: item.estimate_item_detail이 존재할 경우 -->
						<tr v-for="(item_detail, index_detail) in item.estimate_item_detail">
							<td class="  bd-r "> {{item.date}} {{item_detail.detail_name}} </td>
							<td v-if="estimate.is_open === '1'" class="tx-right  bd-r ">{{$common.formatMoney(item_detail.cde_price)}}</td>
							<td class="tx-center  bd-r ">{{item_detail.detail_count}}</td>
							<td v-if="estimate.is_open === '1'" class="tx-right  bd-r">{{$common.formatMoney(item_detail.detail_amount)}}</td>
						</tr>
					</template>
				</template>
		</table>
		<!-- !견적내용 -->

		<!-- 소계 -->
		<!-- 모든 항목 총 합계 (페소) -->
		<table class="table table-invoice-appline-detail tx-14 tx-neobd tx-black bd-y bd-color-appline" style="width:100%">
			<tr>
				<th class=" tx-center  bd-x " style="width:125px;"> 모든 항목 총 합계 <span class="tx-primary">(페소)</span></th>
				<td class="tx-left  tx-neobd " style="width:265px;">
					<div class="d-flex justify-content-between  text-primary">
						<span>PhP</span>
						<span>{{$common.formatMoney($app_estimate.lib_estimate.total_amount)}}</span>
					</div>
				</td>
				<td class="tx-left  bd-r ">

				</td>
			</tr>
		</table>
		<!-- !모든 항목 총 합계 (페소) -->
		<!-- 모든 항목 총 합계 (원화) -->
		<table class="table table-invoice-appline-detail tx-14 tx-neobd tx-black bd-y bd-color-appline" style="width:100%">
			<tr>
				<th class=" tx-center  bd-x " style="width:125px;"> 모든 항목 총 합계 <span class="tx-danger">(원화)</span></th>
				<td class="tx-left  bd-r " style="width:265px;">
					<div class="d-flex justify-content-between  text-danger ">
						<span>₩</span>
						<span v-if="$app_estimate.lib_estimate.total_amount_kr > 0">{{$common.formatMoney($app_estimate.lib_estimate.total_amount_kr)}}</span>
						<span v-else>{{$common.formatMoney(($app_estimate.lib_estimate.total_amount * exchange))}}</span>
					</div>
				</td>
				<th class=" tx-center  bd-r" style="width:110px;"> (편의를 위한) 1인 금액 안내<span class="tx-danger">(원화)</span></th>
				<td class="tx-left  bd-r ">
					<div class="d-flex justify-content-between  text-danger ">
						<span>₩</span>
						<span v-if="$app_estimate.lib_estimate.total_amount_kr > 0">{{ $common.formatMoney(Math.round(($app_estimate.lib_estimate.total_amount_kr / $app_estimate.lib_estimate.guests))) }}</span>
						<span v-else>{{ $common.formatMoney(Math.round(($app_estimate.lib_estimate.total_amount / $app_estimate.lib_estimate.guests) * exchange)) }}</span>
					</div>
				</td>
			</tr>
		</table>
		<!-- !모든 항목 총 합계 (원화)-->
		<!-- 예약금 -->
		<table class="table table-invoice-appline-detail tx-14 tx-neobd tx-black bd-y bd-color-appline" style="width:100%">
			<tr>
				<th class=" tx-center  bd-x tx-danger" rowspan="2" style="width:125px;"> 예약금<br>(1박 + 골프)</th>
				<td class="tx-left  bd-r" rowspan="2" style="width:265px;">
					<div class="d-flex justify-content-between  text-danger ">
						<span>₩</span>
						{{$common.formatMoney(roomresfee_golf_sum)}}
					</div>
				</td>
				<th class=" tx-center  bd-r" style="width:110px;"> 1박금액</th>
				<td class="tx-left  bd-r ">
					<div class="d-flex justify-content-between  tx-gray-700 ">
						<span>₩</span>
						{{$common.formatMoney(computed_resfee)}}
					</div>
				</td>
			</tr>
			<tr>
				<th class=" tx-center  bd-r ">골프금액</th>
				<td class="tx-left  bd-r ">
					<div class="d-flex justify-content-between  tx-gray-700 ">
						<span>₩</span>
						{{$common.formatMoney(computed_golf_amount)}}
					</div>
				</td>
			</tr>
		</table>
		<!-- !예약금-->
		<!-- !소계 -->

		<!-- 입금안내 -->
		<div class="row justify-content-between">
			<div class="col-12">
				<p class="form-text tx-danger">위의 예약금을 아래 계좌로 입금해주세요. 예약금 입금 확인 후 숙박예약과 골프예약이 진행됩니다.
					예약금 입금 계좌: 국민은행, 지형진(웨이블) 408801-01-325455 </p>
			</div>
		</div>
		<!-- !입금안내 -->


		<!-- 잔금 -->
		<table class="table table-invoice-appline-detail tx-14 tx-neobd tx-black bd-y bd-color-appline" style="width:100%">
			<tr>
				<th class=" tx-center bd-x" style="width:125px;">잔금<br><span class="tx-primary">(페소)</span></th>
				<td class="tx-left  bd-r  text-danger" style="width:265px;">
					<div class="d-flex justify-content-between  ">
						<span>PhP</span>
						{{$common.formatMoney(Number($app_estimate.lib_estimate.total_amount - $app_estimate.lib_estimate.room_estimate.room_resfee - $app_estimate.lib_estimate.golf_amount))}}
					</div>
				</td>
				<td class=" tx-center  bd-r "> 숙소 체크인 후 현지 매니저님에게 페소로 결제해 주세요!</td>

			</tr>
		</table>
		<!-- !잔금 -->

		<!-- 안내사항 -->
		<div class="row justify-content-between">
			<div class="col-12">
				<p class="form-text tx-black">1. 견적을 받으신 후 바로 입금 하시는 경우가 아니라면, 입금 전 다시한번 예약가능 여부를 카톡이나 전화로 확인 부탁드립니다.
					<br>2. 풀빌라는 규정상 예약금 입금 후 환불이 불가하오니 신중한 예약 부탁드립니다.
					<br>3. 예약이 확정된 이후에는 변경/취소가 불가하니 일정을 컨펌하신 후 예약진행 부탁드립니다.
					<br>4. 예약금을 제외한 잔금은 현지 도착하셔서 현지 담당자에게 페소로 지불해주세요.
				</p>

			</div>
		</div>
		<!-- !안내사항 -->













		<!-- note -->
		<div v-if="$app_estimate.lib_estimate.remark_1" class="row justify-content-between">
			<div class="col-sm-12 col-lg-12 order-2 order-sm-0 mg-t-0 mg-sm-t-0">
				<label class="tx-sans tx-uppercase tx-10  tx-spacing-1 tx-color-03">Notes</label>
				<p class="form-text tx-danger" v-html="$app_estimate.lib_estimate.remark_1.split('\n').join('<br/>')"> </p>
			</div>
		</div>
		<!-- note -->

	</div>

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
				orientation: 'landscape' // Changed to landscape format
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