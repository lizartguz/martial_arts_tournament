import { copyFile, mkdir } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const vendorAssets = [];

await Promise.all(vendorAssets.map(async ({ source, destination }) => {
    const destinationPath = resolve(projectRoot, destination);

    await mkdir(dirname(destinationPath), { recursive: true });
    await copyFile(resolve(projectRoot, source), destinationPath);
}));
