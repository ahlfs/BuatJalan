<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechStack extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'layer',
        'name',
        'icon',
        'description',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        $knownTechs = [
            'supabase' => ['devicon', 'supabase/supabase-original.svg'],
            'vercel' => ['devicon', 'vercel/vercel-original.svg'],
            'railway' => ['url', 'https://cdn.simpleicons.org/railway'],
            'aws' => ['devicon', 'amazonwebservices/amazonwebservices-original-wordmark.svg'],
            'amazon' => ['devicon', 'amazonwebservices/amazonwebservices-original-wordmark.svg'],
            'laravel' => ['devicon', 'laravel/laravel-original.svg'],
            'react' => ['devicon', 'react/react-original.svg'],
            'next' => ['devicon', 'nextjs/nextjs-original.svg'],
            'astro' => ['devicon', 'astro/astro-original.svg'],
            'node' => ['devicon', 'nodejs/nodejs-original.svg'],
            'express' => ['devicon', 'express/express-original.svg'],
            'postgres' => ['devicon', 'postgresql/postgresql-original.svg'],
            'mysql' => ['devicon', 'mysql/mysql-original.svg'],
            'mongo' => ['devicon', 'mongodb/mongodb-original.svg'],
            'python' => ['devicon', 'python/python-original.svg'],
            'go' => ['devicon', 'go/go-original.svg'],
            'vue' => ['devicon', 'vuejs/vuejs-original.svg'],
            'flutter' => ['devicon', 'flutter/flutter-original.svg'],
            'docker' => ['devicon', 'docker/docker-original.svg'],
            'tailwind' => ['devicon', 'tailwindcss/tailwindcss-original.svg'],
            'kotlin' => ['devicon', 'kotlin/kotlin-original.svg'],
            'typescript' => ['devicon', 'typescript/typescript-original.svg'],
            'ts' => ['devicon', 'typescript/typescript-original.svg'],
            'javascript' => ['devicon', 'javascript/javascript-original.svg'],
            'js' => ['devicon', 'javascript/javascript-original.svg'],
            'java' => ['devicon', 'java/java-original.svg'],
            'spring' => ['devicon', 'spring/spring-original.svg'],
            'swift' => ['devicon', 'swift/swift-original.svg'],
            'firebase' => ['devicon', 'firebase/firebase-original.svg'],
            'redis' => ['devicon', 'redis/redis-original.svg'],
            'php' => ['devicon', 'php/php-original.svg'],
            'ruby' => ['devicon', 'ruby/ruby-original.svg'],
            'html' => ['devicon', 'html5/html5-original.svg'],
            'css' => ['devicon', 'css3/css3-original.svg'],
            'stripe' => ['url', 'https://cdn.simpleicons.org/stripe/635BFF'],
            'github' => ['devicon', 'github/github-original.svg'],
            'gitlab' => ['devicon', 'gitlab/gitlab-original.svg'],
            'bitbucket' => ['devicon', 'bitbucket/bitbucket-original.svg'],
            'digitalocean' => ['devicon', 'digitalocean/digitalocean-original.svg'],
            'kubernetes' => ['devicon', 'kubernetes/kubernetes-original.svg'],
            'nginx' => ['devicon', 'nginx/nginx-original.svg'],
            'apache' => ['devicon', 'apache/apache-original.svg'],
            'django' => ['devicon', 'django/django-plain.svg'],
            'flask' => ['devicon', 'flask/flask-original.svg'],
            'angular' => ['devicon', 'angular/angular-original.svg'],
            'svelte' => ['devicon', 'svelte/svelte-original.svg'],
            'sass' => ['devicon', 'sass/sass-original.svg'],
            'figma' => ['devicon', 'figma/figma-original.svg'],
            'c++' => ['devicon', 'cplusplus/cplusplus-original.svg'],
            'cpp' => ['devicon', 'cplusplus/cplusplus-original.svg'],
            'c#' => ['devicon', 'csharp/csharp-original.svg'],
            'csharp' => ['devicon', 'csharp/csharp-original.svg'],
            'gemini' => ['url', 'https://cdn.simpleicons.org/googlegemini/8E75C2'],
            'openai' => ['url', 'https://cdn.simpleicons.org/openai/412991'],
            'deepseek' => ['url', 'https://cdn.simpleicons.org/deepseek/0054FF'],
        ];

        $nameLower = strtolower($this->name);
        foreach ($knownTechs as $keyword => $config) {
            if (str_contains($nameLower, $keyword)) {
                if ($config[0] === 'url') {
                    return $config[1];
                }
                return "https://cdn.jsdelivr.net/gh/devicons/devicon/icons/{$config[1]}";
            }
        }

        return null;
    }
}
