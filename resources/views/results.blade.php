<?php
/**
 * @var \Illuminate\Database\Eloquent\Collection $resultItems
 */
?>
@extends('~layout')

@section('content')
	<div class="page-content">
		<section class="b-search-page">
			<h1 class="b-search-page__header">@lang('sitesearch::messages.result_header')</h1>
			<div class="b-search-page__result b-search-results">
				@if ($message)
					<div class="b-search-page__message">{{ $message }}</div>
				@endif
				@if ($resultItems && $resultItems->count() > 0)
					@foreach($resultItems as $item)
						<?php /** @var \App\SiteSearch\Contract\IndexItem $item */ ?>
						<div class="b-search-results__item">
							@include('sitesearch::result')
						</div>
					@endforeach
				@endif
			</div>
			<div class="b-search-page__pager">
				@include ('pager ~ site')
			</div>
		</section>
	</div>
@endsection
