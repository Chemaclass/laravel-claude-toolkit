<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel Claude Toolkit') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=jetbrains-mono:400,500,600,700&display=swap" rel="stylesheet" />
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.tsx'])
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
            .terminal-path { font-size: 0.75rem; color: #6b7280; margin-left: 0.5rem; text-decoration: none; transition: color 0.2s; }
            .terminal-path:hover { color: #4ade80; }
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
            details { margin: 0.25rem 0; }
            summary { cursor: pointer; list-style: none; }
            summary::-webkit-details-marker { display: none; }
            summary::before { content: '▶ '; color: #6b7280; font-size: 0.625rem; transition: transform 0.2s; display: inline-block; width: 1rem; }
            details[open] summary::before { content: '▼ '; }
            summary:hover .table-name { text-decoration: underline; }
            .example-box { background-color: #111827; border-left: 2px solid rgba(34, 197, 94, 0.4); margin: 0.5rem 0 0.5rem 1rem; padding: 0.75rem; font-size: 0.7rem; }
            .example-label { color: #6b7280; font-size: 0.625rem; text-transform: uppercase; margin-bottom: 0.25rem; }
            .example-input { color: #22d3ee; }
            .example-output { color: #86efac; white-space: pre-wrap; }
            .example-file { color: #facc15; font-size: 0.65rem; }
            .cmd-row { display: flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0; }
            .cmd-row:hover { background-color: rgba(34, 197, 94, 0.05); }
            .cmd-row .copy-inline { opacity: 0; background: transparent; border: none; color: #6b7280; cursor: pointer; font-size: 0.625rem; padding: 0.125rem 0.25rem; transition: opacity 0.2s; flex-shrink: 0; }
            .cmd-row:hover .copy-inline { opacity: 1; }
            .cmd-row .copy-inline:hover { color: #4ade80; }
            .cmd-row .copy-inline.copied { opacity: 1; color: #4ade80; }
            .cmd-row code { flex: 1; }
            .config-row { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(107, 114, 128, 0.2); }
            .config-field { display: flex; align-items: center; gap: 0.5rem; }
            .config-field label { color: #6b7280; font-size: 0.7rem; }
            .config-field input { background: #030712; border: 1px solid rgba(34, 197, 94, 0.4); color: #facc15; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-family: inherit; border-radius: 0.25rem; width: 8rem; }
            .config-field input:focus { outline: none; border-color: #4ade80; }
            .config-field input::placeholder { color: rgba(250, 204, 21, 0.4); }
            .config-field select { background: #030712; border: 1px solid rgba(34, 197, 94, 0.4); color: #4ade80; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-family: inherit; border-radius: 0.25rem; cursor: pointer; appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%234ade80' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.4rem center; padding-right: 1.5rem; }
            .config-field select:focus { outline: none; border-color: #4ade80; }
            .config-field select:hover { border-color: #4ade80; }
            .config-field select option { background: #030712; color: #4ade80; }
            .port-input { width: 4rem !important; }
            .copy-all-btn { background: transparent; border: 1px solid rgba(34, 197, 94, 0.5); color: #4ade80; padding: 0.25rem 0.75rem; font-size: 0.65rem; font-family: inherit; cursor: pointer; border-radius: 0.25rem; transition: all 0.2s; margin-left: auto; white-space: nowrap; }
            .copy-all-btn:hover { background-color: rgba(34, 197, 94, 0.1); border-color: #4ade80; }
            .copy-all-btn.copied { background-color: rgba(34, 197, 94, 0.2); }
            .config-inputs { display: flex; flex-wrap: wrap; gap: 1rem; }
            .localhost-hint { margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid rgba(34, 197, 94, 0.2); font-size: 0.75rem; animation: fadeIn 0.3s ease-in; }
            .localhost-link { color: #4ade80; text-decoration: none; }
            .localhost-link:hover { text-decoration: underline; }
            @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
            .bracket { color: #6b7280; }
            .link-text { color: #4ade80; }
            .terminal-footer { background-color: #111827; padding: 0.5rem 1rem; font-size: 0.75rem; color: #4b5563; border-top: 1px solid rgba(34, 197, 94, 0.2); }
            .footer-prompt { color: rgba(34, 197, 94, 0.5); }
            .heart { color: #ef4444; }
            .coffee { color: #eab308; }
            .connect-section { text-align: center; margin-top: 1.5rem; }
            .connect-title { color: #6b7280; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.75rem; }
            .connect-links { display: flex; flex-wrap: wrap; justify-content: center; gap: 0.5rem; }
            .connect-link { padding: 0.375rem 0.75rem; border: 1px solid rgba(34, 197, 94, 0.5); border-radius: 0.25rem; text-decoration: none; font-size: 0.75rem; transition: all 0.2s; }
            .connect-link:hover { background-color: rgba(34, 197, 94, 0.1); border-color: #4ade80; }
            .copyright { text-align: center; color: #4b5563; font-size: 0.7rem; margin-top: 1rem; }
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
                    <a href="https://github.com/Chemaclass/laravel-claude-toolkit" target="_blank" class="terminal-path">~/chemaclass/laravel-claude-toolkit</a>
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

                    <!-- Why Section -->
                    <div class="section-divider">
                        <span class="prompt">#</span> <span class="section-title">WHY THIS TOOLKIT?</span>
                    </div>

                    <div>
                        <p class="cmd-line">
                            <span class="prompt">$</span>
                            <span class="cmd">cat</span> <span class="file">problem.txt</span>
                        </p>
                        <p class="output">
                            Laravel projects grow into tangled monoliths where adding features becomes risky and testing becomes painful.
                        </p>
                    </div>

                    <div style="margin-top: 1rem;">
                        <p class="cmd-line">
                            <span class="prompt">$</span>
                            <span class="cmd">cat</span> <span class="file">solution.txt</span>
                        </p>
                        <p class="output">
                            Structured patterns (DDD, Hexagonal) + AI guardrails that enforce consistency and guide architectural decisions.
                        </p>
                    </div>

                    <div class="highlight-tags" style="margin-top: 1rem;">
                        <span class="highlight-tag">Testable</span>
                        <span class="highlight-tag">|</span>
                        <span class="highlight-tag">Maintainable</span>
                        <span class="highlight-tag">|</span>
                        <span class="highlight-tag">Scalable</span>
                        <span class="highlight-tag">|</span>
                        <span class="highlight-tag">AI-Guided</span>
                    </div>

                    <!-- Quick Start Section -->
                    <div class="section-divider">
                        <span class="prompt">#</span> <span class="section-title">QUICK START</span>
                    </div>

                    <div class="code-block" id="quick-start">
                        <div class="config-row">
                            <div class="config-inputs">
                                <div class="config-field">
                                    <label for="project-name">Project:</label>
                                    <input type="text" id="project-name" value="my-app" placeholder="my-app" oninput="updateCommands()">
                                </div>
                                <div class="config-field">
                                    <label for="repo-visibility">Visibility:</label>
                                    <select id="repo-visibility" onchange="updateCommands()">
                                        <option value="public">public</option>
                                        <option value="private">private</option>
                                    </select>
                                </div>
                                <div class="config-field">
                                    <label for="app-port">App:</label>
                                    <input type="number" id="app-port" value="8085" class="port-input" oninput="updateCommands()">
                                </div>
                                <div class="config-field">
                                    <label for="vite-port">Vite:</label>
                                    <input type="number" id="vite-port" value="5174" class="port-input" oninput="updateCommands()">
                                </div>
                            </div>
                            <button class="copy-all-btn" onclick="copyAll(this)">COPY ALL</button>
                        </div>
                        <div class="cmd-row">
                            <span class="prompt">$</span>
                            <code id="cmd-1"><span class="cmd">gh repo create</span> <span class="file">my-app</span> <span class="cmd">--template</span> Chemaclass/laravel-claude-toolkit <span class="cmd">--public --clone</span></code>
                            <button class="copy-inline" onclick="copyCmd(this, 1)" title="Copy">copy</button>
                        </div>
                        <div class="cmd-row">
                            <span class="prompt">$</span>
                            <code id="cmd-2"><span class="cmd">cd</span> <span class="file">my-app</span> && <span class="cmd">composer setup</span></code>
                            <button class="copy-inline" onclick="copyCmd(this, 2)" title="Copy">copy</button>
                        </div>
                        <div class="cmd-row">
                            <span class="prompt">$</span>
                            <code id="cmd-3"><span class="cmd">APP_PORT=</span><span class="file">8085</span> <span class="cmd">VITE_PORT=</span><span class="file">5174</span> <span class="cmd">./vendor/bin/sail up -d</span></code>
                            <button class="copy-inline" onclick="copyCmd(this, 3)" title="Copy">copy</button>
                        </div>
                        <div id="localhost-hint" class="localhost-hint" style="display: none;">
                            <span style="color: #6b7280;">After running, your app will be available at</span> <a id="localhost-url" href="http://localhost:8085" target="_blank" class="localhost-link">http://localhost:8085</a>
                        </div>
                    </div>

                    <div class="stack-badges">
                        <span class="badge">PHP 8.4</span>
                        <span class="badge">Laravel 12</span>
                        <span class="badge">React 19</span>
                        <span class="badge">TypeScript</span>
                        <span class="badge">Inertia.js</span>
                        <span class="badge">Tailwind CSS 4</span>
                        <span class="badge">Vitest</span>
                        <span class="badge">ESLint</span>
                        <span class="badge">SQLite</span>
                        <span class="badge">Sail</span>
                    </div>

                    <!-- Quality Tooling Section -->
                    <div class="section-divider">
                        <span class="prompt">#</span> <span class="section-title">QUALITY TOOLING</span>
                    </div>

                    <div>
                        <div class="table-header">TOOL                        DESCRIPTION</div>
                        <div style="border-bottom: 1px solid rgba(107, 114, 128, 0.3); margin-bottom: 0.5rem;"></div>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">PHPStan (max level)</span>
                                <span class="table-desc">Static analysis with Larastan</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Run:</div>
                                <div class="example-input">composer phpstan</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Features:</div>
                                <div class="example-output">- Level max (strictest analysis)
- Laravel-aware via Larastan
- Catches type errors before runtime
- Analyzes app/ and modules/</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">Rector</span>
                                <span class="table-desc">Automated refactoring & upgrades</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Run:</div>
                                <div class="example-input">composer rector      # dry-run</div>
                                <div class="example-input">composer rector:fix  # apply changes</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Features:</div>
                                <div class="example-output">- Auto-upgrade PHP syntax
- Dead code removal
- Code quality improvements
- Consistent coding patterns</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">Pint</span>
                                <span class="table-desc">Laravel code style fixer</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Run:</div>
                                <div class="example-input">composer lint  # check style</div>
                                <div class="example-input">composer fix   # auto-fix</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Features:</div>
                                <div class="example-output">- Laravel coding standards
- PSR-12 compliant
- Automatic formatting
- Zero configuration needed</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">PHPUnit</span>
                                <span class="table-desc">PHP testing framework</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Run:</div>
                                <div class="example-input">./vendor/bin/phpunit</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Features:</div>
                                <div class="example-output">- Unit, Integration & Feature tests
- Parallel test execution
- Code coverage reports
- Laravel testing helpers</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">ESLint</span>
                                <span class="table-desc">TypeScript & React linting</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Run:</div>
                                <div class="example-input">npm run lint      # check</div>
                                <div class="example-input">npm run lint:fix  # auto-fix</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Features:</div>
                                <div class="example-output">- TypeScript-ESLint strict rules
- React Hooks linting
- React Refresh compatibility
- No any, no console.log</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">TypeScript</span>
                                <span class="table-desc">Static type checking for frontend</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Run:</div>
                                <div class="example-input">npm run typecheck</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Features:</div>
                                <div class="example-output">- Strict mode enabled
- Path aliases (@/ → resources/js/)
- React JSX support
- Catches type errors before runtime</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">Vitest</span>
                                <span class="table-desc">Frontend testing framework</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Run:</div>
                                <div class="example-input">npm run test:unit   # run once</div>
                                <div class="example-input">npm run test:watch  # watch mode</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Features:</div>
                                <div class="example-output">- Component smoke tests
- React Testing Library
- Co-located test files
- Factory-based test props</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">Git Hooks</span>
                                <span class="table-desc">Parallel pre-commit & pre-push gates</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Auto-configured:</div>
                                <div class="example-input">composer setup  # enables hooks</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Pre-commit (parallel):</div>
                                <div class="example-output">Phase 1: Pint + Rector + PHPStan + ESLint + TypeScript
Phase 2: PHPUnit + Vitest (after analysis passes)</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Pre-push (parallel):</div>
                                <div class="example-output">Full backend (composer test) + frontend (npm run test)</div>
                            </div>
                        </details>
                    </div>

                    <!-- Claude Code Agents Section -->
                    <div class="section-divider">
                        <span class="prompt">#</span> <span class="section-title">CLAUDE CODE AGENTS</span>
                    </div>

                    <div>
                        <div class="table-header">AGENT                       PURPOSE</div>
                        <div style="border-bottom: 1px solid rgba(107, 114, 128, 0.3); margin-bottom: 0.5rem;"></div>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">domain-architect</span>
                                <span class="table-desc">DDD & hexagonal architecture guidance</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">You ask:</div>
                                <div class="example-input">"How should I structure the Order module?"</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Agent helps with:</div>
                                <div class="example-output">- Entity design (Order, OrderLine, OrderId)
- Value objects (Money, Quantity, Address)
- Repository interface placement
- Module boundaries & dependencies</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">tdd-coach</span>
                                <span class="table-desc">Red-green-refactor workflow coaching</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">You ask:</div>
                                <div class="example-input">"Help me TDD a discount calculator"</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Agent guides you through:</div>
                                <div class="example-output">1. RED: Write failing test for 10% discount
2. GREEN: Implement minimum code to pass
3. REFACTOR: Extract discount strategy pattern
4. Repeat for edge cases (max discount, stacking)</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">clean-code-reviewer</span>
                                <span class="table-desc">SOLID principles & code smell detection</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">You ask:</div>
                                <div class="example-input">"Review my OrderService class"</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Agent identifies:</div>
                                <div class="example-output">- SRP violation: Split payment logic to PaymentService
- DIP violation: Depend on interface, not Eloquent model
- Long method: Extract validateOrder() helper
- Missing null check on optional discount</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">security-reviewer</span>
                                <span class="table-desc">OWASP Top 10 & vulnerability detection</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">You ask:</div>
                                <div class="example-input">"Check the auth module for vulnerabilities"</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Agent checks:</div>
                                <div class="example-output">- SQL injection & XSS vectors
- CSRF protection coverage
- Mass assignment vulnerabilities
- Hardcoded secrets & credentials</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">react-reviewer</span>
                                <span class="table-desc">React/TypeScript frontend quality review</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">You ask:</div>
                                <div class="example-input">"Review the dashboard components"</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Agent checks:</div>
                                <div class="example-output">- React pattern compliance
- UI component consistency (shadcn/ui)
- Dark mode coverage
- TypeScript strict mode violations</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">changelog-keeper</span>
                                <span class="table-desc">Changelog maintenance & formatting</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">You ask:</div>
                                <div class="example-input">"Update the changelog with recent changes"</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Agent handles:</div>
                                <div class="example-output">- User-focused entry writing
- Change categorization (Feature/Fix/Breaking)
- Date section management
- Consistent formatting</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">explorer</span>
                                <span class="table-desc">Fast read-only codebase search & analysis</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">You ask:</div>
                                <div class="example-input">"Find all repository implementations"</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Agent provides:</div>
                                <div class="example-output">- File pattern matching
- Dependency mapping between modules
- Directory structure analysis
- Quick codebase navigation</div>
                            </div>
                        </details>
                    </div>

                    <!-- Claude Code Commands Section -->
                    <div class="section-divider">
                        <span class="prompt">#</span> <span class="section-title">CLAUDE CODE COMMANDS</span>
                    </div>

                    <div>
                        <div class="table-header">COMMAND                     GENERATES</div>
                        <div style="border-bottom: 1px solid rgba(107, 114, 128, 0.3); margin-bottom: 0.5rem;"></div>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">/create-entity</span>
                                <span class="table-desc">Domain entity + value objects + test</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Input:</div>
                                <div class="example-input">/create-entity Order Order</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Creates:</div>
                                <div class="example-file">modules/Order/Domain/Entity/Order.php</div>
                                <div class="example-file">modules/Order/Domain/ValueObject/OrderId.php</div>
                                <div class="example-file">tests/Unit/Order/Domain/Entity/OrderTest.php</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">/create-repository</span>
                                <span class="table-desc">Interface + Eloquent + InMemory impls</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Input:</div>
                                <div class="example-input">/create-repository Order Order</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Creates:</div>
                                <div class="example-file">modules/Order/Domain/Repository/OrderRepository.php</div>
                                <div class="example-file">modules/Order/Infrastructure/Persistence/Eloquent/OrderEloquentRepository.php</div>
                                <div class="example-file">modules/Order/Infrastructure/Persistence/InMemory/OrderInMemoryRepository.php</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">/create-use-case</span>
                                <span class="table-desc">Command/Query DTO + Handler + test</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Input:</div>
                                <div class="example-input">/create-use-case Order Command CreateOrder</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Creates:</div>
                                <div class="example-file">modules/Order/Application/Command/CreateOrder.php</div>
                                <div class="example-file">modules/Order/Application/Command/CreateOrderHandler.php</div>
                                <div class="example-file">tests/Unit/Order/Application/Command/CreateOrderHandlerTest.php</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">/create-controller</span>
                                <span class="table-desc">Thin controller + request + resource</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Input:</div>
                                <div class="example-input">/create-controller Order Order</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Creates:</div>
                                <div class="example-file">modules/Order/Infrastructure/Http/Controller/OrderController.php</div>
                                <div class="example-file">modules/Order/Infrastructure/Http/Request/CreateOrderRequest.php</div>
                                <div class="example-file">modules/Order/Infrastructure/Http/Resource/OrderResource.php</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">/tdd-cycle</span>
                                <span class="table-desc">Interactive red-green-refactor guide</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Input:</div>
                                <div class="example-input">/tdd-cycle</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Guides you through:</div>
                                <div class="example-output">Phase 1: RED - Write a failing test first
Phase 2: GREEN - Write minimal code to pass
Phase 3: REFACTOR - Improve while tests pass
Then loops back to RED for next behavior</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">/refactor-check</span>
                                <span class="table-desc">SOLID violations & improvement report</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Input:</div>
                                <div class="example-input">/refactor-check modules/Order/</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Reports:</div>
                                <div class="example-output">- Classes with too many dependencies
- Methods exceeding complexity threshold
- Missing interface abstractions
- Suggested refactoring patterns</div>
                            </div>
                        </details>

                        <div style="border-top: 1px solid rgba(107, 114, 128, 0.2); margin: 0.75rem 0 0.5rem 0; padding-top: 0.25rem;">
                            <div class="table-header">FRONTEND SCAFFOLDING</div>
                        </div>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">/create-page</span>
                                <span class="table-desc">Inertia.js React page + test + factory</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Input:</div>
                                <div class="example-input">/create-page Order Show</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Creates:</div>
                                <div class="example-file">resources/js/pages/Order/Show.tsx</div>
                                <div class="example-file">resources/js/pages/Order/Show.test.tsx</div>
                                <div class="example-file">resources/js/test/factories/order.ts</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">/create-component</span>
                                <span class="table-desc">React subcomponent with barrel export</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Input:</div>
                                <div class="example-input">/create-component Order ItemsCard</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Creates:</div>
                                <div class="example-file">resources/js/pages/Order/components/ItemsCard.tsx</div>
                                <div class="example-file">resources/js/pages/Order/components/index.ts</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">/create-hook</span>
                                <span class="table-desc">Custom React hook with co-located test</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Input:</div>
                                <div class="example-input">/create-hook use-theme</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Creates:</div>
                                <div class="example-file">resources/js/hooks/use-theme.ts</div>
                                <div class="example-file">resources/js/hooks/use-theme.test.ts</div>
                            </div>
                        </details>

                        <div style="border-top: 1px solid rgba(107, 114, 128, 0.2); margin: 0.75rem 0 0.5rem 0; padding-top: 0.25rem;">
                            <div class="table-header">QUALITY & REVIEW</div>
                        </div>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">/code-review</span>
                                <span class="table-desc">Architecture & quality compliance review</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Input:</div>
                                <div class="example-input">/code-review modules/Order/</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Reviews:</div>
                                <div class="example-output">- Hexagonal architecture compliance
- Naming convention adherence
- Test coverage gaps
- Code smell detection</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">/security-review</span>
                                <span class="table-desc">OWASP Top 10 security analysis</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Input:</div>
                                <div class="example-input">/security-review modules/User/</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Checks:</div>
                                <div class="example-output">- SQL injection & XSS vectors
- CSRF & mass assignment protection
- Hardcoded secrets detection
- Authentication/authorization gaps</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">/ui-review</span>
                                <span class="table-desc">Frontend design consistency review</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Input:</div>
                                <div class="example-input">/ui-review resources/js/pages/Order/</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Checks:</div>
                                <div class="example-output">- Dark mode coverage (every bg/text/border)
- UI component usage (no raw HTML)
- Accessibility & interactive states
- Theme consistency</div>
                            </div>
                        </details>

                        <div style="border-top: 1px solid rgba(107, 114, 128, 0.2); margin: 0.75rem 0 0.5rem 0; padding-top: 0.25rem;">
                            <div class="table-header">WORKFLOW</div>
                        </div>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">/test</span>
                                <span class="table-desc">Smart test runner with module filtering</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Input:</div>
                                <div class="example-input">/test Order</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Runs:</div>
                                <div class="example-output">- Filters tests by module or class name
- Runs both PHPUnit and Vitest as needed
- Shows clear pass/fail summary</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">/fix</span>
                                <span class="table-desc">Auto-fix all code quality issues</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Input:</div>
                                <div class="example-input">/fix</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Runs:</div>
                                <div class="example-output">- Pint (PHP code style)
- Rector (PHP refactoring)
- ESLint --fix (TypeScript/React)
- Reports remaining manual fixes</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">/pr</span>
                                <span class="table-desc">Create PR with auto-generated description</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Input:</div>
                                <div class="example-input">/pr Add order cancellation</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Creates:</div>
                                <div class="example-output">- Branch from current changes
- PR with summary & test plan
- Changelog entry
- Pushes and opens PR URL</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">/gh-issue</span>
                                <span class="table-desc">Fetch GitHub issue & plan implementation</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Input:</div>
                                <div class="example-input">/gh-issue 42</div>
                                <div class="example-label" style="margin-top: 0.5rem;">Does:</div>
                                <div class="example-output">- Fetches issue details from GitHub
- Analyzes requirements & affected modules
- Proposes implementation plan
- Creates branch and starts work</div>
                            </div>
                        </details>
                    </div>

                    <!-- Claude Code Skills Section -->
                    <div class="section-divider">
                        <span class="prompt">#</span> <span class="section-title">CLAUDE CODE SKILLS</span>
                    </div>

                    <div>
                        <div class="table-header">SKILL                       PROVIDES</div>
                        <div style="border-bottom: 1px solid rgba(107, 114, 128, 0.3); margin-bottom: 0.5rem;"></div>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">create-entity</span>
                                <span class="table-desc">Domain entity scaffolding templates</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Skill provides:</div>
                                <div class="example-output">- final readonly class pattern
- Private constructor + static factory
- Invariant validation in create()
- Identity value object (EntityId)</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">create-repository</span>
                                <span class="table-desc">Repository pattern implementations</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Skill provides:</div>
                                <div class="example-output">- Interface in Domain layer
- Eloquent implementation for production
- InMemory implementation for tests
- Service provider bindings</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">create-use-case</span>
                                <span class="table-desc">CQRS handler templates & best practices</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Skill provides:</div>
                                <div class="example-output">- Command/Query DTO with readonly props
- Handler with __invoke() method
- Repository injection pattern
- Unit test with InMemory repository</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">create-controller</span>
                                <span class="table-desc">HTTP layer scaffolding</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Skill provides:</div>
                                <div class="example-output">- Thin controller (validate, dispatch, respond)
- Form Request for validation rules
- API Resource for transformation
- Route registration example</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">tdd-cycle</span>
                                <span class="table-desc">Test-driven development workflow</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Skill provides:</div>
                                <div class="example-output">- RED: Test naming conventions
- GREEN: Minimal implementation tips
- REFACTOR: When & how to refactor
- Test isolation best practices</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">refactor-check</span>
                                <span class="table-desc">Code quality analysis rules</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Skill analyzes:</div>
                                <div class="example-output">- Single Responsibility violations
- Dependency Inversion issues
- Method complexity metrics
- Coupling between modules</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">create-page</span>
                                <span class="table-desc">Inertia.js React page scaffolding</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Skill provides:</div>
                                <div class="example-output">- AppLayout/GuestLayout integration
- Head title & breadcrumbs pattern
- Co-located test with factory props
- Inertia page resolution conventions</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">create-component</span>
                                <span class="table-desc">React subcomponent patterns</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Skill provides:</div>
                                <div class="example-output">- Named export (not default)
- Props interface with TypeScript
- Barrel re-export in index.ts
- Dark mode & accessibility patterns</div>
                            </div>
                        </details>

                        <details>
                            <summary class="table-row">
                                <span class="table-name">create-hook</span>
                                <span class="table-desc">Custom React hook templates</span>
                            </summary>
                            <div class="example-box">
                                <div class="example-label">Skill provides:</div>
                                <div class="example-output">- use-{name}.ts naming convention
- Co-located test with renderHook
- TypeScript return type definition
- Proper cleanup patterns</div>
                            </div>
                        </details>
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

                    <div class="code-block tree">
                        <p><span class="tree-folder">resources/js/</span></p>
                        <p>├── <span class="tree-folder">pages/{Module}/</span>  <span class="tree-comment"># Inertia.js React pages + tests</span></p>
                        <p>├── <span class="tree-folder">layouts/</span>         <span class="tree-comment"># AppLayout, GuestLayout</span></p>
                        <p>├── <span class="tree-folder">lib/</span>             <span class="tree-comment"># Utilities (cn, formatting)</span></p>
                        <p>└── <span class="tree-folder">test/</span>            <span class="tree-comment"># Mocks, setup, factories</span></p>
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
                        <span class="highlight-tag">|</span>
                        <span class="highlight-tag">React + Inertia.js</span>
                    </div>

                </div>

                <div class="terminal-footer">
                    <span class="footer-prompt">></span> Ready to build something awesome?<span class="terminal-cursor">_</span>
                </div>
            </div>

            <div class="connect-section">
                <p class="connect-title">Connect</p>
                <div class="connect-links">
                    <a href="https://github.com/Chemaclass" target="_blank" class="connect-link">
                        <span class="bracket">[</span><span class="link-text">GitHub</span><span class="bracket">]</span>
                    </a>
                    <a href="https://twitter.com/Chemaclass" target="_blank" class="connect-link">
                        <span class="bracket">[</span><span class="link-text">Twitter</span><span class="bracket">]</span>
                    </a>
                    <a href="https://www.linkedin.com/in/jose-maria-valera-reales/" target="_blank" class="connect-link">
                        <span class="bracket">[</span><span class="link-text">LinkedIn</span><span class="bracket">]</span>
                    </a>
                    <a href="https://chemaclass.com/sponsor/" target="_blank" class="connect-link">
                        <span class="bracket">[</span><span class="link-text">Sponsor</span><span class="bracket">]</span>
                    </a>
                </div>
                <p class="copyright">© {{ date('Y') }} Chemaclass · Made with <span class="heart">&lt;3</span> and <span class="coffee">coffee</span></p>
            </div>
        </main>

        <script>
            function getProjectName() {
                return document.getElementById('project-name').value || 'my-app';
            }

            function getAppPort() {
                return document.getElementById('app-port').value || '8085';
            }

            function getVitePort() {
                return document.getElementById('vite-port').value || '5174';
            }

            function getRepoVisibility() {
                return document.getElementById('repo-visibility').value || 'public';
            }

            function getCommand(num) {
                const name = getProjectName();
                const appPort = getAppPort();
                const vitePort = getVitePort();
                const visibility = getRepoVisibility();
                switch(num) {
                    case 1: return `gh repo create ${name} --template Chemaclass/laravel-claude-toolkit --${visibility} --clone`;
                    case 2: return `cd ${name} && composer setup`;
                    case 3: return `APP_PORT=${appPort} VITE_PORT=${vitePort} ./vendor/bin/sail up -d`;
                }
            }

            function updateCommands() {
                const name = getProjectName();
                const appPort = getAppPort();
                const vitePort = getVitePort();
                const visibility = getRepoVisibility();

                document.getElementById('cmd-1').innerHTML =
                    `<span class="cmd">gh repo create</span> <span class="file">${name}</span> <span class="cmd">--template</span> Chemaclass/laravel-claude-toolkit <span class="cmd">--${visibility} --clone</span>`;

                document.getElementById('cmd-2').innerHTML =
                    `<span class="cmd">cd</span> <span class="file">${name}</span> && <span class="cmd">composer setup</span>`;

                document.getElementById('cmd-3').innerHTML =
                    `<span class="cmd">APP_PORT=</span><span class="file">${appPort}</span> <span class="cmd">VITE_PORT=</span><span class="file">${vitePort}</span> <span class="cmd">./vendor/bin/sail up -d</span>`;
            }

            function copyCmd(btn, num) {
                const cmd = getCommand(num);
                navigator.clipboard.writeText(cmd).then(() => {
                    btn.textContent = 'copied!';
                    btn.classList.add('copied');
                    setTimeout(() => {
                        btn.textContent = 'copy';
                        btn.classList.remove('copied');
                    }, 1500);
                });
            }

            function copyAll(btn) {
                const commands = [getCommand(1), getCommand(2), getCommand(3)].join('\n');
                const appPort = getAppPort();
                const url = `http://localhost:${appPort}`;

                navigator.clipboard.writeText(commands).then(() => {
                    btn.textContent = 'COPIED!';
                    btn.classList.add('copied');

                    const hint = document.getElementById('localhost-hint');
                    const link = document.getElementById('localhost-url');
                    link.href = url;
                    link.textContent = url;
                    hint.style.display = 'block';

                    setTimeout(() => {
                        btn.textContent = 'COPY ALL';
                        btn.classList.remove('copied');
                    }, 1500);
                });
            }
        </script>
    </body>
</html>
