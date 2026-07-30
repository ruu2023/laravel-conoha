<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $post->title }}</title>
</head>
<body>
	<h1>{{ $post->title }}</h1>

	<p>投稿日時: {{ $post->created_at }}</p>
	<hr />

	<div>
		{!! nl2br(e($post->content)) !!}
	</div>

	<hr />
	{{-- 一覧にもどる --}}
	<a href="{{ route('posts.index') }}">ニュース一覧に戻る</a>
</body>
</html>
