/**
 * serve-folder.mjs — preview ANY generated landing folder locally via WASM PHP.
 * Usage:  node serve-folder.mjs <absolute-folder> [port]
 * No system PHP needed. Networking is off, so kclient won't fire — you see the page itself.
 */
import http from 'node:http';
import { loadNodeRuntime, createNodeFsMountHandler } from '@php-wasm/node';
import { PHP, PHPRequestHandler } from '@php-wasm/universal';

const DOC_ROOT = process.argv[2] || process.env.LPF_DOCROOT;
const PORT = Number(process.env.PORT) || Number(process.argv[3]) || 8001;
if (!DOC_ROOT) { console.error('Usage: node serve-folder.mjs <absolute-folder> [port]'); process.exit(1); }

const VFS = '/site';
console.log('• Booting WASM PHP to serve:', DOC_ROOT);
const php = new PHP(await loadNodeRuntime('8.3', { emscriptenOptions: { processId: 1 } }));
try { php.mkdir(VFS); } catch {}
await php.mount(VFS, createNodeFsMountHandler(DOC_ROOT));
const handler = new PHPRequestHandler({ php, documentRoot: VFS, absoluteUrl: `http://localhost:${PORT}` });

http.createServer(async (req, res) => {
  try {
    const chunks = []; for await (const c of req) chunks.push(c);
    const body = Buffer.concat(chunks);
    let url = req.url || '/'; if (url === '' || url === '/') url = '/index.php';
    const r = await handler.request({
      url, method: req.method || 'GET', headers: req.headers,
      body: body.length ? new Uint8Array(body) : undefined,
    });
    res.statusCode = r.httpStatusCode;
    for (const [k, v] of Object.entries(r.headers || {})) { try { res.setHeader(k, v); } catch {} }
    res.end(Buffer.from(r.bytes));
  } catch (e) { res.statusCode = 500; res.end('preview error: ' + (e && e.stack ? e.stack : e)); }
}).listen(PORT, () => console.log(`  landing preview → http://localhost:${PORT}`));
