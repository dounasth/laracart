@foreach ($categories as $category)
<option value="{{ $category->id }}" {{ ($category->id && ($category->id == $selected || ( is_array($selected) && in_array($category->id, $selected) ) )) ? "selected" : '' }}>{{ $prefix }} {{ $category->title }} </option>
@if (fn_is_not_empty($category->children()->get()))
@include('laracart::categories.select-options', array('categories'=>$category->children()->get(), 'selected'=>$selected, 'prefix'=>$prefix.'|---'))
@endif
@endforeach