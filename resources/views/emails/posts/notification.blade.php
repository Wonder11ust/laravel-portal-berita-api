<x-mail::message>
# Artikel Baru: {{ $post->title }}
{{ Str::limit($post->content, 100) }}

<x-mail::button :url="'http://127.0.0.1:8000/api/test-posts/' . $post->id">
    Baca selengkapnya
</x-mail::button>


Terima kasih.<br>
{{ config('app.name') }}
</x-mail::message>
