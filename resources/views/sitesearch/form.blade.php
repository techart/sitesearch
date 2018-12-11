<form class="b-search-form" action="{{ route('search_result') }}">
    {!! csrf_field() !!}
    <input class="b-search-form__query-input" name="q">
    <input class="b-search-form__submit" type="submit" value="@lang('search')">
</form>