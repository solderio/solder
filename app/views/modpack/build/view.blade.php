@extends('layouts/master')
@section('top')
    {{ HTML::script('js/selectize.min.js')}}
    {{ HTML::style('css/selectize.css')}}
@endsection
@section('content')
<div class="page-header">
<h1>Build Management</h1>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	<div class="pull-right">
		<a href="{{ URL::current() }}" class="btn btn-xs btn-warning">Refresh</a>
	    <a href="{{ URL::to('modpack/view/' . $build->modpack->id) }}" class="btn btn-xs btn-info">Back to Modpack</a>
	</div>
	Build Management: {{ $build->modpack->name }} - Build {{ $build->version }}
	</div>
	<div class="panel-body">
		<div class="alert alert-success" id="success-ajax" style="width: 100%;display: none">
		</div>
		<div class="table-responsive">
		<table class="table">
			<thead>
				<th style="width: 60%">Add a Mod</th>
				<th></th>
				<th></th>
			</thead>
			<tbody>
			<form method="post" action="{{ URL::to('modpack/build/modify') }}" class="mod-add">
			<input type="hidden" name="build" value="{{ $build->id }}">
			<input type="hidden" name="action" value="add">
			<tr id="mod-list-add">
				<td>
					<i class="icon-plus"></i>
					<select class="form-control" name="mod-name" id="mod" placeholder="Select a Mod">
						@foreach (Mod::all() as $mod)
						<option value="{{ $mod->name }}">{{ $mod->pretty_name }}</option>
						@endforeach
					</select>
				</td>
				<td>
					<select class="form-control" name="mod-version" id="mod-version" placeholder="Select a Modversion">
					</select>
				</td>
				<td>
					<button type="submit" class="btn btn-success btn-small">Add Mod</button>
				</td>
			</tr>
			</form>
			</tbody>
		</table>
		<table class="table" id="mod-list">
			<thead>
				<th id="mod-header" style="width: 60%">Mod Name</th>
				<th>Version</th>
				<th></th>
			</thead>
			<tbody>
				@foreach ($build->modversions->sortByDesc('build_id', SORT_NATURAL) as $ver)
				<tr>
					<td>{{ HTML::link('mod/view/'.$ver->mod->id, $ver->mod->pretty_name) }} ({{ $ver->mod->name }})</td>
					<td>
						<form method="post" action="{{ URL::to('modpack/build/modify') }}" style="margin-bottom: 0" class="mod-version">
							<input type="hidden" class="build-id" name="build_id" value="{{ $build->id }}">
							<input type="hidden" class="modversion-id" name="modversion_id" value="{{ $ver->pivot->modversion_id }}">
							<input type="hidden" name="action" value="version">
							<div class="form-group input-group">
								<select class="form-control" name="version">
									@foreach ($ver->mod->versions as $version)
									<option value="{{ $version->id }}"{{ $selected = ($ver->version == $version->version ? 'selected' : '') }}>{{ $version->version }}</option>
									@endforeach
								</select>
								<span class="input-group-btn">
									<button type="submit" class="btn btn-primary">Change</button>
								</span>
							</div>
						</form>
					</td>
					<td>
						<form method="post" action="{{ URL::to('modpack/build/modify') }}" style="margin-bottom: 0" class="mod-delete">
							<input type="hidden" name="build_id" value="{{ $build->id }}">
							<input type="hidden" class="modversion-id" name="modversion_id" value="{{ $ver->pivot->modversion_id }}">
							<input type="hidden" name="action" value="delete">
							<button type="submit" class="btn btn-danger btn-small">Remove</button>
						</form>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
		</div>
	</div>
</div>
@endsection
@section('bottom')
<script type="text/javascript">

var $select = $("#mod").selectize({
			persist: false,
			maxItems: 1,
			sortField: {
				field: 'text',
				direction: 'asc'
			},
		});
var mod = $select[0].selectize;
var $select = $("#mod-version").selectize({
			persist: false,
			maxItems: 1,
			sortField: {
					field: 'text',
					direction: 'asc'
				},
		});
var modversion = $select[0].selectize;

$(".mod-version").submit(function(e) {
	e.preventDefault();
	$.ajax({
		type: "POST",
		url: "{{ URL::to('modpack/modify/version') }}",
		data: $(this).serialize(),
		success: function (data) {
			$("#success-ajax").stop(true, true).html("Version Updated").fadeIn().delay(2000).fadeOut();
		}
	});
});

$(".mod-delete").submit(function(e) {
	e.preventDefault();
	$.ajax({
		type: "POST",
		url: "{{ URL::to('modpack/modify/delete') }}",
		data: $(this).serialize(),
		success: function (data) {
			//
		}
	});
	$(this).parent().parent().fadeOut();
});

$(".mod-add").submit(function(e) {
	e.preventDefault();
	$.ajax({
		type: "POST",
		url: "{{ URL::to('modpack/modify/add') }}",
		data: $(this).serialize(),
		success: function (data) {
			$("#mod-list-add").before('<tr><td>' + data.pretty_name + '</td><td>' + data.version + '</td><td></td></tr>');
		}
	});
});

function refreshModVersions() {
	$.ajax({
		type: "GET",
		url: "{{ URL::to('api/mod/') }}/" + mod.getValue(),
		success: function (data) {
			modversion.clear();
			$(data.versions).each(function(e, m) {
				modversion.addOption({value: m, text: m});
				modversion.refreshOptions(false);
			});
		}
	});
}

mod.on('change', refreshModVersions);
</script>
<script type="text/javascript">
$( document ).ready(function() {
	$("#mod-list").dataTable({
    	"order": [[ 0, "asc" ]],
    	"autoWidth": false,
    	"columnDefs": [
			{ "width": "60%", "targets": 0 },
			{ "width": "30%", "targets": 1 }
		]
    });
    refreshModVersions();
});
</script>
@endsection
