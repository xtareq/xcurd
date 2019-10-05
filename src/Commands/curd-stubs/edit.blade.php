@extends('layout.app')


@section('content')

<div class="row">
	<form action="{{route('model.update',$model)}}" method="post" enctype="multipart/form-data">
		@csrf
		@method('PUT')
		{!!$form!!}
		<button type="submit" class="btn btn-success pull-right"> <i class="fa fa-check"></i>Update</button>
	</form>
</div>

@endsection