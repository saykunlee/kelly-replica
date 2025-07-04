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
	<div v-else id="invoice" class='estimate_a4 pd-10'>
		<!-- 헤더부분 시작  -->
		<div class="d-flex mb-2">
			<!-- 로고 -->
			<div class="pd-10 wd-20p flex-fill d-flex align-items-center justify-content-center"><!-- logo area start  -->
				<div class="carousel slide" data-ride="carousel">
					<div class="carousel-inner">
						<div class="carousel-item active">
							<!-- brand-logo start -->
							<img :src="'/' + $app_estimate.lib_estimate.partner_info.image1" height="120" class="d-block mx-auto">
							<!-- brand-logo end -->
						</div>
						<!-- more items goes here -->
					</div>
				</div>
				<!-- logo area end -->
			</div>
			<!-- 로고 -->
			<!-- 기본정보 -->
			<div class="pd-10 flex-fill">
				<table class="table table-invoice-info bd-b tx-nowrap mg-b-0">

					<tr class="">
						<td style="width:150px !important;" class="tx-nowrap tx-center bd-r bg-gray-100  ">
							<span class="  ">견적일자 </span>
						</td>
						<td class=" tx-center  "> {{$app_estimate.lib_estimate.insert_date_ymd}}</td>
					</tr>
					<tr>
						<td class="tx-nowrap tx-center bd-r bg-gray-100">
							<span class="">성함 </span>
						</td>
						<td class=" tx-center  "> {{$app_estimate.lib_estimate.guest_name}}</td>
					</tr>
					<tr>
						<td class="tx-nowrap tx-center bd-r bg-gray-100">
							<span class="">인원 </span>
						</td>
						<td class=" tx-center  "> {{$app_estimate.lib_estimate.guests}}인</td>
					</tr>
					<tr style="">
						<td class="tx-nowrap tx-center bd-r bg-gray-100">
							<span class="">숙박일정 </span>
						</td>
						<td class=" tx-center  "> {{$app_estimate.lib_estimate.room_estimate.res_info.exp_ci_date}} - {{$app_estimate.lib_estimate.room_estimate.res_info.exp_co_date}}
							( {{$app_estimate.lib_estimate.room_estimate.res_info.nights}}박 )</td>
					</tr>
				</table>
			</div>
			<!-- 기본정보 -->
		</div>
		<!-- 헤더부분 끝  -->

		<!-- 견적내용 -->
		<div class="table-responsive mg-t-0">
			<table class="table table-invoice bd-b  mg-b-0">
				<thead>
					<tr class="bg-gray-100" style="border-top: 2px solid #99a0ae; border-bottom: 1px solid #99a0ae;">
						<th class="tx-center bd-r" style="width: 170px;">항목</th>
						<th class="tx-center bd-r " style="width: 500px;">내용</th>
						<th v-if="estimate.is_open === '1'" class="tx-center bd-r" style="width: 125px;">기본요금(페소)</th>
						<th class="wd-100 tx-center bd-r">일수/인원</th>
						<th v-if="estimate.is_open === '1'" class="tx-center" style="width:190px">개별항목 총 요금(페소)</th>
					</tr>
				</thead>
				<tbody>
					<!-- 숙박 -->
					<template v-if="$app_estimate.lib_estimate.room_estimate.room_checked.length > 0">
						<tr v-for="(item, index) in $app_estimate.lib_estimate.room_estimate.room_checked">
							<td class="  tx-center bd-t-0-f bd-r">
								<span class="tx-15 tx-bold">숙박 </span>
								<br>
								<small class="form-text text-danger">***체크인 30일 전까지 예약 취소/환불 가능</small>
							</td>
							<td class=" tx-medium bd-t-0-f bd-r tx-15">
								> {{item.room_type_nm}}
								<small v-if="item.accoms_type_nm !== '호텔' " class="form-text">(익일 새벽 체크인, 1룸&1인 조식 포함)</small>
							</td>
							<td v-if="estimate.is_open === '1'" class="tx-right tx-medium bd-t-0-f bd-r tx-15">{{$common.formatMoney($app_estimate.get_single_room_charge)}}</td>
							<td class="tx-center tx-medium bd-t-0-f bd-r tx-15">{{item.nights}}</td>
							<td v-if="estimate.is_open === '1'" class="tx-right tx-medium bd-t-0-f tx-15">{{$common.formatMoney(item.total_amount)}}

							</td>
						</tr>
					</template>
					<!-- 차량 -->
					<template v-if="groupedEstimates.length > 0">

						<tr v-for="(item, index) in groupedEstimates">
							<td class="tx-center bd-r" v-if="index === 0" :rowspan="groupedEstimates.length">
								<span class="tx-15 tx-bold">차량 </span>
							</td>
							<td class=" tx-medium bd-r tx-15">
								> {{item.key_car_name}}
								<small class="form-text">{{item.key_description}}</small>
								<template v-for="(item_detail, index_detail) in item.items">
									<small class="form-text">{{item_detail.remark_1}} "{{item_detail.rental_date}}" {{item_detail.rental_counts}}대</small>
								</template>


							</td>
							<td v-if="estimate.is_open === '1'" class="tx-right tx-medium bd-r tx-15">{{$common.formatMoney(item.key_cost_per_night)}}</td>
							<td class="tx-center tx-medium bd-r tx-15">{{item.key_sum_rental_days}}
								<!-- <template v-if="item.key_sum_rental_counts > 1">/{{item.key_sum_rental_counts}}</template> -->
							</td>
							<td v-if="estimate.is_open === '1'" class="tx-right tx-medium tx-15">{{$common.formatMoney(item.key_sum_rental_amount)}}</td>
						</tr>
					</template>
					<!-- !차량 -->

					<!-- 골프 -->
					<template v-if="$app_estimate.lib_estimate.golf_estimate.length > 0">
						<tr v-for="(item, index) in $app_estimate.lib_estimate.golf_estimate">
							<td class="tx-center bd-r" v-if="index === 0" :rowspan="$app_estimate.lib_estimate.golf_estimate.length">
								<span class="tx-15 tx-bold">골프 </span>
								<small class="form-text text-danger">( 카트/캐디/보험/의무사용 포함 )</small>
								<small class="form-text text-danger">*플레이 인원 홀수인 경우, 싱글 카트 비용 추가</small>
							</td>
							<td class=" tx-medium bd-r tx-15"> > {{item.golf_date}} {{item.golf_name}} <!-- {{item.golf_course}} --> </td>
							<td v-if="estimate.is_open === '1'" class="tx-right tx-medium bd-r tx-15">{{$common.formatMoney(item.greenfee)}}</td>
							<td class="tx-center tx-medium bd-r tx-15">{{item.golf_people}}</td>
							<td v-if="estimate.is_open === '1'" class="tx-right tx-medium tx-15">{{$common.formatMoney(item.golf_amount)}}</td>
						</tr>
					</template>
					<!-- !골프 -->
					<!-- 기타여행 -->
					<template v-if="$app_estimate.lib_estimate.item_estimate.length > 0">
						<template v-for="(item, index) in $app_estimate.lib_estimate.item_estimate">
							<!-- 첫 번째 행 -->
							<tr>
								<td class="tx-nowrap tx-center bd-r" v-if="index === 0" :rowspan="getRowSpan($app_estimate.lib_estimate.item_estimate)">
									<span class="tx-15 tx-bold">기타</span>
								</td>
								<td class=" tx-medium bd-r tx-15"> > {{item.date}} {{item.name}}
									<template v-if="item.cit_bigo1">
										({{removeHtmlTags(item.cit_bigo1)}})
									</template>
								</td>
								<td v-if="estimate.is_open === '1'" class="tx-right tx-medium bd-r tx-15">{{$common.formatMoney(item.cit_regular_price)}}</td>
								<!-- <td class="tx-right tx-medium bd-r tx-15">{{$common.formatMoney(item.item_price)}}</td> -->
								<td class="tx-center tx-medium bd-r tx-15">{{item.people}}</td>
								<!-- <td class="tx-right tx-medium tx-15">{{$common.formatMoney(item.item_amount)}}</td> -->
								<td v-if="estimate.is_open === '1'" class="tx-right tx-medium tx-15">{{$common.formatMoney(item.item_amount_without_detail)}}</td>
							</tr>
							<!-- 추가된 부분: item.estimate_item_detail이 존재할 경우 -->
							<tr v-for="(item_detail, index_detail) in item.estimate_item_detail">
								<td class=" tx-medium bd-r tx-15"> > {{item.date}} {{item_detail.detail_name}} </td>
								<td v-if="estimate.is_open === '1'" class="tx-right tx-medium bd-r tx-15">{{$common.formatMoney(item_detail.cde_price)}}</td>
								<td class="tx-center tx-medium bd-r tx-15">{{item_detail.detail_count}}</td>
								<td v-if="estimate.is_open === '1'" class="tx-right tx-medium tx-15">{{$common.formatMoney(item_detail.detail_amount)}}</td>
							</tr>
						</template>
					</template>
			</table>


			<!-- 예약금, 요약 -->
			<table class="table table-invoice bd-b  mg-b-0">
				<tr style="border-top: 3px double #99a0ae;">
					<td class=" tx-center tx-medium bd-r" style="width:238px;"> 예약금</td>
					<td class="tx-left tx-medium bd-r">
						아래 예약금을 입금해주시면 예약 확정됩니다.

						<div class="d-flex justify-content-start mg-b-0">
							- 숙소<!-- (<span class="tx-14 tx-medium ">{{resfee_type_nm}}</span>) -->:
							<span class="mg-l-5">₩</span>
							<span v-if="$app_estimate.lib_estimate.room_estimate.room_resfee_kr > 0">{{$common.formatMoney(($app_estimate.lib_estimate.room_estimate.room_resfee_kr))}}</span>
							<span v-else>{{$common.formatMoney(($app_estimate.lib_estimate.room_estimate.room_resfee * exchange))}}</span>
							월
						</div>
						<div class="d-flex justify-content-start mg-b-0">
							- 골프:
							<span class="mg-l-5">₩</span>
							<span v-if="$app_estimate.lib_estimate.golf_amount_kr > 0">{{$common.formatMoney($app_estimate.lib_estimate.golf_amount_kr)}}</span>
							<span v-else> {{$common.formatMoney($app_estimate.lib_estimate.golf_amount * exchange)}}</span>
							월
						</div>
						<div class="d-flex justify-content-start mg-b-0">
							- 차량:
							<span class="mg-l-5">₩</span>
							<span v-if="$app_estimate.lib_estimate.rental_car_amount_kr > 0">{{$common.formatMoney($app_estimate.lib_estimate.rental_car_amount_kr)}}</span>
							<span v-else> {{$common.formatMoney($app_estimate.lib_estimate.rental_car_amount * exchange)}}</span>
							월
						</div>
						<div class="d-flex justify-content-start mg-b-0">
							- 합계: 위의 모든 항목 총 예약금인
							<span class="mg-l-5">₩</span>
							<span>{{$common.formatMoney($app_estimate.lib_estimate.rental_car_amount_kr + $app_estimate.lib_estimate.rental_car_amount_kr + $app_estimate.lib_estimate.golf_amount_kr)}}</span>
							원을 아래 계좌로 입금해주세요.
						</div>
						<div class="d-flex justify-content-start mg-b-0">
							예약금 입금 계좌: 국민은행, 지형진(웨이틀), 408801-01-325455
						</div>
					</td>
					<td class="tx-center tx-medium " style="width:162px; padding:0px;" rowspan="2">
						<table class="table table-invoice pd-0 bd-b  mg-b-0">
							<tr class="bg-gray-100">
								<td style="border-bottom: 3px solid #99a0ae;">모든 항목 총 합계(페소)</td>
							</tr>
							<tr>
								<td class=" tx-center tx-medium " style="height:75px;">
									<div class="d-flex justify-content-between tx-medium text-danger tx-16">
										<span>PhP</span>
										<span>{{$common.formatMoney($app_estimate.lib_estimate.total_amount)}}</span>
									</div>

								</td>
							</tr>
							<tr class="bg-gray-100">
								<td style="border-bottom: 3px solid #99a0ae;">모든 항목 총 합계(원화)</td>
							</tr>
							<tr>
								<td class=" tx-center tx-medium " style="height:75px; position: relative; z-index: 1;">
									<div class="d-flex justify-content-between tx-medium text-danger tx-16">
										<span>₩</span>
										<span v-if="$app_estimate.lib_estimate.total_amount_kr > 0">{{$common.formatMoney($app_estimate.lib_estimate.total_amount_kr)}}</span>
										<span v-else>{{$common.formatMoney(($app_estimate.lib_estimate.total_amount * exchange))}}</span>
									</div>
								</td>
							</tr>
							<tr class="bg-gray-100">
								<td style="border-bottom: 3px solid #99a0ae;">1인 금액 (원화)</td>
							</tr>
							<tr>
								<td class=" tx-center tx-medium " style="height:75px;">
									<div class="d-flex justify-content-between tx-medium text-danger tx-16">
										<span>₩</span>
										<span v-if="$app_estimate.lib_estimate.total_amount_kr > 0">{{ $common.formatMoney(Math.round(($app_estimate.lib_estimate.total_amount_kr / $app_estimate.lib_estimate.guests))) }}</span>
										<span v-else>{{ $common.formatMoney(Math.round(($app_estimate.lib_estimate.total_amount / $app_estimate.lib_estimate.guests) * exchange)) }}</span>
									</div>
								</td>
							</tr>


						</table>
					</td>
				</tr>
				<tr style="border-bottom: 3px double #99a0ae;">
					<td class=" tx-center tx-medium bd-r"> 안내사항</td>
					<td class="tx-left tx-medium bd-r">
						1. 견적을 받으신 후 바로 입금하시는 경우가 아니라면, 입금 전 다시한번 예약가능 여부를 카톡이나 전화로 확인 부탁드립니다.
						<br>2. 풀빌라는 규정상 예약금 입금 후 환불이 불가하오니 신중한 예약 부탁드립니다.
						<br>3. 예약이 확정된 이후에는 변경/취소가 불가하니 일정을 컨펌하신 후 예약진행 부탁드립니다.
						<br>4. 예약금을 제외한 잔금은 현지 도착하셔서 현지 담당자에게 페소로 지불해주세요.
					</td>
				</tr>
			</table>
			<!-- !예약금, 요약 -->

		</div>
		<div class="row justify-content-between">
			<div class="col-sm-12 col-lg-12 order-2 order-sm-0 mg-t-0 mg-sm-t-0">
				<label class="tx-sans tx-uppercase tx-10 tx-medium tx-spacing-1 tx-color-03">Notes</label>
				<p class="form-text tx-danger" v-html="$app_estimate.lib_estimate.remark_1.split('\n').join('<br/>')"> </p>
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
		let filename = '견적서_' + view_data.data.guest_name + '님(' + view_data.data.room_estimate.res_info.exp_ci_date + "~" + view_data.data.room_estimate.res_info.exp_ci_date +").pdf" ; // replace this with the actual variable
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
		let filename = '견적서_' + view_data.data.guest_name + '님(' + view_data.data.room_estimate.res_info.exp_ci_date + "~" + view_data.data.room_estimate.res_info.exp_ci_date +").jpg" ; // replace this with the actual variable
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