@props(['data' => []])

@shows('body', $data['body'] ?? null)
    <div @class(['prose-cms', 'max-w-3xl' => ($data['width'] ?? 'prose') === 'prose']) @editable('body')>{{ rich_text($data['body'] ?? null) }}</div>
@endshows
