@extends('layout.app')


@section('content')

<div class="row">
	<form action="{{route('model.store')}}" method="post" enctype="multipart/form-data">
		@csrf
		{!!$form!!}

		<button type="submit" class="btn btn-success pull-right"> <i class="fa fa-check"></i>Save</button>
	</form>
</div>

@endsection