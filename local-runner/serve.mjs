/**
 * Local runner for White LP Factory — runs the PHP app with NO system PHP needed.
 * Uses WordPress Playground's WASM PHP (@php-wasm) under Node.
 *
 *   cd local-runner && npm install && npm start
 *   → open http://localhost:8000
 *
 * For production just upload the parent folder to any real PHP host; this runner
 * is only for previewing/using the generator locally.
 */
import http from 'node:http';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { loadNodeRuntime, createNodeFsMountHandler, withNetworking } from '@php-wasm/node';
import { PHP, PHPRequestHandler } from '@php-wasm/universal';

const PORT = Number(process.env.PORT) || 8000;
const PROJECT_DIR = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const VFS = '/app';

console.log('• Booting WASM PHP 8.3 …');
let emscriptenOptions = { processId: 1 };
let networking = true;
try {
  emscriptenOptions = await withNetworking(emscriptenOptions); // enables curl/auto-fetch
} catch (e) {
  networking = false;
  console.log('  (networking unavailable — auto-fetch will fall back to manual entry)');
}

const php = new PHP(await loadNodeRuntime('8.3', { emscriptenOptions }));
try { php.mkdir(VFS); } catch {}
await php.mount(VFS, createNodeFsMountHandler(PROJECT_DIR));

const handler = new PHPRequestHandler({
  php,
  documentRoot: VFS,
  absoluteUrl: `http://localhost:${PORT}`,
});

const server = http.createServer(async (req, res) => {
  try {
    const chunks = [];
    for await (const c of req) chunks.push(c);
    const body = Buffer.concat(chunks);

    let url = req.url || '/';
    if (url === '/' || url === '') url = '/index.php';

    const resp = await handler.request({
      url,
      method: req.method || 'GET',
      headers: req.headers,
      body: body.length ? new Uint8Array(body) : undefined,
    });

    res.statusCode = resp.httpStatusCode;
    for (const [k, v] of Object.entries(resp.headers || {})) {
      try { res.setHeader(k, v); } catch { /* skip illegal header */ }
    }
    res.end(Buffer.from(resp.bytes));
  } catch (e) {
    res.statusCode = 500;
    res.setHeader('content-type', 'text/plain; charset=utf-8');
    res.end('Local runner error:\n' + (e && e.stack ? e.stack : e));
  }
});

server.listen(PORT, () => {
  console.log(`\n  ┌────────────────────────────────────────────┐`);
  console.log(`  │  White LP Factory                            │`);
  console.log(`  │  → http://localhost:${PORT}${' '.repeat(Math.max(0, 24 - String(PORT).length))}│`);
  console.log(`  │  networking: ${networking ? 'on (auto-fetch live) ' : 'off (manual entry)   '}        │`);
  console.log(`  └────────────────────────────────────────────┘`);
  console.log('  Ctrl+C to stop.\n');
});
