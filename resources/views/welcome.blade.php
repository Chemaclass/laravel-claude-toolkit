<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel Claude Toolkit') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=jetbrains-mono:400,500,600,700&display=swap" rel="stylesheet" />
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <style>
            * { box-sizing: border-box; }
            body {
                font-family: 'JetBrains Mono', monospace;
                background-color: #030712;
                color: #4ade80;
                min-height: 100vh;
                padding: 1.5rem;
                margin: 0;
            }
            .terminal-cursor {
                animation: blink 1s step-end infinite;
            }
            @keyframes blink {
                50% { opacity: 0; }
            }
            main { max-width: 56rem; width: 100%; margin: 0 auto; }
            .terminal-window { border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 0.5rem; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(34, 197, 94, 0.1); margin-bottom: 1.5rem; }
            .terminal-header { background-color: #111827; padding: 0.5rem 1rem; display: flex; align-items: center; gap: 0.5rem; border-bottom: 1px solid rgba(34, 197, 94, 0.2); }
            .terminal-dots { display: flex; gap: 0.375rem; }
            .terminal-dot { width: 0.75rem; height: 0.75rem; border-radius: 50%; }
            .dot-red { background-color: rgba(239, 68, 68, 0.8); }
            .dot-yellow { background-color: rgba(234, 179, 8, 0.8); }
            .dot-green { background-color: rgba(34, 197, 94, 0.8); }
            .terminal-path { font-size: 0.75rem; color: #6b7280; margin-left: 0.5rem; }
            .terminal-content { background-color: #030712; padding: 1.5rem; }
            .terminal-content > * + * { margin-top: 1rem; }
            pre { font-size: 0.75rem; line-height: 1.25; margin: 0; white-space: pre-wrap; word-wrap: break-word; }
            .cmd-line { display: flex; gap: 0.5rem; flex-wrap: wrap; }
            .prompt { color: #6b7280; }
            .cmd { color: #22d3ee; }
            .file { color: #facc15; }
            .output { padding-left: 1rem; color: #d1d5db; }
            .section-divider { color: #6b7280; margin: 1.5rem 0 1rem 0; font-size: 0.75rem; }
            .section-title { color: #22d3ee; font-weight: 600; }
            .table-header { color: #6b7280; font-size: 0.75rem; margin-bottom: 0.25rem; }
            .table-row { display: flex; font-size: 0.75rem; padding: 0.25rem 0; }
            .table-name { color: #facc15; min-width: 10rem; flex-shrink: 0; }
            .table-desc { color: #d1d5db; }
            .stack-badges { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem; }
            .badge { padding: 0.25rem 0.5rem; border: 1px solid rgba(34, 197, 94, 0.4); border-radius: 0.25rem; font-size: 0.625rem; color: #86efac; }
            .tree { font-size: 0.75rem; color: #d1d5db; }
            .tree-folder { color: #facc15; }
            .tree-comment { color: #6b7280; font-style: italic; }
            .highlight-tags { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.75rem; }
            .highlight-tag { color: #a78bfa; font-size: 0.75rem; }
            .links { padding-top: 1rem; border-top: 1px solid rgba(34, 197, 94, 0.2); display: flex; flex-wrap: wrap; gap: 0.75rem; font-size: 0.75rem; margin-top: 1rem; }
            .link { padding: 0.375rem 0.75rem; border: 1px solid rgba(34, 197, 94, 0.5); border-radius: 0.25rem; text-decoration: none; transition: all 0.2s; }
            .link:hover { background-color: rgba(34, 197, 94, 0.1); border-color: #4ade80; }
            .bracket { color: #6b7280; }
            .link-text { color: #4ade80; }
            .terminal-footer { background-color: #111827; padding: 0.5rem 1rem; font-size: 0.75rem; color: #4b5563; border-top: 1px solid rgba(34, 197, 94, 0.2); }
            .footer-prompt { color: rgba(34, 197, 94, 0.5); }
            .page-footer { text-align: center; color: #4b5563; font-size: 0.75rem; margin-top: 1.5rem; }
            .heart { color: #ef4444; }
            .coffee { color: #eab308; }
            .code-block { background-color: #111827; border-radius: 0.25rem; padding: 0.75rem 1rem; margin: 0.5rem 0; font-size: 0.75rem; overflow-x: auto; }
            .code-block .cmd { color: #22d3ee; }
            .code-block .comment { color: #6b7280; }
            @media (min-width: 640px) {
                pre { font-size: 0.875rem; }
                .table-row { font-size: 0.875rem; }
                .table-name { min-width: 12rem; }
            }
            @media (max-width: 639px) {
                .table-row { flex-direction: column; gap: 0.125rem; }
                .table-name { min-width: auto; }
                pre.ascii-art { font-size: 0.5rem; }
            }
        </style>
    </head>
    <body>
        <main>
            <div class="terminal-window">
                <div class="terminal-header">
                    <div class="terminal-dots">
                        <span class="terminal-dot dot-red"></span>
                        <span class="terminal-dot dot-yellow"></span>
                        <span class="terminal-dot dot-green"></span>
                    </div>
                    <span class="terminal-path">~/chemaclass/laravel-claude-toolkit</span>
                </div>

                <div class="terminal-content">
                    <!-- Hero Section -->
                    <pre class="ascii-art">
 _                              _    ____ _                 _
| |    __ _ _ __ __ ___   _____| |  / ___| | __ _ _   _  __| | ___
| |   / _` | '__/ _` \ \ / / _ \ | | |   | |/ _` | | | |/ _` |/ _ \
| |__| (_| | | | (_| |\ V /  __/ | | |___| | (_| | |_| | (_| |  __/
|_____\__,_|_|  \__,_| \_/ \___|_|  \____|_|\__,_|\__,_|\__,_|\___|
                          _____ ___   ___  _     _  _____ _____
                         |_   _/ _ \ / _ \| |   | |/ /_ _|_   _|
                           | || | | | | | | |   | ' / | |  | |
                           | || |_| | |_| | |___| . \ | |  | |
                           |_| \___/ \___/|_____|_|\_\___| |_|
                    </pre>

                    <div>
                        <p class="cmd-line">
                            <span class="prompt">$</span>
                            <span class="cmd">whoami</span>
                        </p>
                        <p class="output">
                            Laravel starter kit for AI-assisted modular development
                        </p>
                    </div>

                    <!-- Quick Start Section -->
                    <div class="section-divider">
                        <span class="prompt">#</span> <span class="section-title">QUICK START</span>
                    </div>

                    <div class="code-block">
                        <p><span class="prompt">$</span> <span class="cmd">gh repo create</span> my-project <span class="cmd">--template</span> Chemaclass/laravel-claude-toolkit</p>
                        <p><span class="prompt">$</span> <span class="cmd">cd</span> my-project && <span class="cmd">composer setup</span></p>
                        <p><span class="prompt">$</span> <span class="cmd">./vendor/bin/sail up -d</span></p>
                    </div>

                    <div class="stack-badges">
                        <span class="badge">PHP 8.4</span>
                        <span class="badge">Laravel 12</span>
                        <span class="badge">SQLite</span>
                        <span class="badge">Tailwind CSS 4</span>
                        <span class="badge">Sail</span>
                    </div>

                    <!-- Claude Code Agents Section -->
                    <div class="section-divider">
                        <span class="prompt">#</span> <span class="section-title">CLAUDE CODE AGENTS</span>
                    </div>

                    <div>
                        <div class="table-header">AGENT                       PURPOSE</div>
                        <div style="border-bottom: 1px solid rgba(107, 114, 128, 0.3); margin-bottom: 0.5rem;"></div>
                        <div class="table-row">
                            <span class="table-name">domain-architect</span>
                            <span class="table-desc">DDD & hexagonal architecture guidance</span>
                        </div>
                        <div class="table-row">
                            <span class="table-name">tdd-coach</span>
                            <span class="table-desc">Red-green-refactor workflow coaching</span>
                        </div>
                        <div class="table-row">
                            <span class="table-name">clean-code-reviewer</span>
                            <span class="table-desc">SOLID principles & code smell detection</span>
                        </div>
                    </div>

                    <!-- Claude Code Commands Section -->
                    <div class="section-divider">
                        <span class="prompt">#</span> <span class="section-title">CLAUDE CODE COMMANDS</span>
                    </div>

                    <div>
                        <div class="table-header">COMMAND                     GENERATES</div>
                        <div style="border-bottom: 1px solid rgba(107, 114, 128, 0.3); margin-bottom: 0.5rem;"></div>
                        <div class="table-row">
                            <span class="table-name">/create-entity</span>
                            <span class="table-desc">Domain entity + value objects + test</span>
                        </div>
                        <div class="table-row">
                            <span class="table-name">/create-repository</span>
                            <span class="table-desc">Interface + Eloquent + InMemory impls</span>
                        </div>
                        <div class="table-row">
                            <span class="table-name">/create-use-case</span>
                            <span class="table-desc">Command/Query DTO + Handler + test</span>
                        </div>
                        <div class="table-row">
                            <span class="table-name">/create-controller</span>
                            <span class="table-desc">Thin controller + request + resource</span>
                        </div>
                        <div class="table-row">
                            <span class="table-name">/tdd-cycle</span>
                            <span class="table-desc">Interactive red-green-refactor guide</span>
                        </div>
                        <div class="table-row">
                            <span class="table-name">/refactor-check</span>
                            <span class="table-desc">SOLID violations & improvement report</span>
                        </div>
                    </div>

                    <!-- Claude Code Skills Section -->
                    <div class="section-divider">
                        <span class="prompt">#</span> <span class="section-title">CLAUDE CODE SKILLS</span>
                    </div>

                    <div>
                        <div class="table-header">SKILL                       PROVIDES</div>
                        <div style="border-bottom: 1px solid rgba(107, 114, 128, 0.3); margin-bottom: 0.5rem;"></div>
                        <div class="table-row">
                            <span class="table-name">create-entity</span>
                            <span class="table-desc">Domain entity scaffolding templates</span>
                        </div>
                        <div class="table-row">
                            <span class="table-name">create-repository</span>
                            <span class="table-desc">Repository pattern implementations</span>
                        </div>
                        <div class="table-row">
                            <span class="table-name">create-use-case</span>
                            <span class="table-desc">CQRS handler templates & best practices</span>
                        </div>
                        <div class="table-row">
                            <span class="table-name">create-controller</span>
                            <span class="table-desc">HTTP layer scaffolding</span>
                        </div>
                        <div class="table-row">
                            <span class="table-name">tdd-cycle</span>
                            <span class="table-desc">Test-driven development workflow</span>
                        </div>
                        <div class="table-row">
                            <span class="table-name">refactor-check</span>
                            <span class="table-desc">Code quality analysis rules</span>
                        </div>
                    </div>

                    <!-- Architecture Preview Section -->
                    <div class="section-divider">
                        <span class="prompt">#</span> <span class="section-title">ARCHITECTURE</span>
                    </div>

                    <div class="code-block tree">
                        <p><span class="tree-folder">modules/{Module}/</span></p>
                        <p>├── <span class="tree-folder">Domain/</span>          <span class="tree-comment"># Pure PHP entities & value objects</span></p>
                        <p>├── <span class="tree-folder">Application/</span>     <span class="tree-comment"># Command/Query handlers (CQRS)</span></p>
                        <p>└── <span class="tree-folder">Infrastructure/</span>  <span class="tree-comment"># Laravel adapters & HTTP layer</span></p>
                    </div>

                    <div class="highlight-tags">
                        <span class="highlight-tag">Modular Monolith</span>
                        <span class="highlight-tag">|</span>
                        <span class="highlight-tag">Hexagonal</span>
                        <span class="highlight-tag">|</span>
                        <span class="highlight-tag">DDD</span>
                        <span class="highlight-tag">|</span>
                        <span class="highlight-tag">TDD</span>
                        <span class="highlight-tag">|</span>
                        <span class="highlight-tag">SOLID</span>
                    </div>

                    <!-- Links Section -->
                    <div class="links">
                        <a href="https://github.com/Chemaclass/laravel-claude-toolkit" target="_blank" class="link">
                            <span class="bracket">[</span><span class="link-text">GitHub</span><span class="bracket">]</span>
                        </a>
                        <a href="https://laravel.com/docs" target="_blank" class="link">
                            <span class="bracket">[</span><span class="link-text">Laravel Docs</span><span class="bracket">]</span>
                        </a>
                        <a href="https://chemaclass.com" target="_blank" class="link">
                            <span class="bracket">[</span><span class="link-text">@Chemaclass</span><span class="bracket">]</span>
                        </a>
                    </div>
                </div>

                <div class="terminal-footer">
                    <span class="footer-prompt">></span> Ready to build something awesome?<span class="terminal-cursor">_</span>
                </div>
            </div>

            <p class="page-footer">
                Made with <span class="heart">&lt;3</span> and a lot of <span class="coffee">coffee</span>
            </p>
        </main>
    </body>
</html>
