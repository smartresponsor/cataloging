// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
// Admin UI DnD entry (framework-agnostic sample). Comments are in English only.
export type MovePolicy = 'preserveSlug' | 'rebuildSlug';

export async function dryRunMove(payload: {
  nodeId: string;
  newParentId: string;
  treeId: string;
  policy: MovePolicy;
  locale?: string;
}): Promise<{ok:boolean; changedCount:number; warnings:string[]; redirects:{from:string;to:string;locale?:string}[]}> {
  const r = await fetch('/admin/category/tree/move', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({...payload, dryRun: true})
  });
  return await r.json();
}

export async function commitMove(payload: {
  nodeId: string;
  newParentId: string;
  treeId: string;
  policy: MovePolicy;
  locale?: string;
}): Promise<{ok:boolean; changedCount:number; warnings:string[]; redirects:{from:string;to:string;locale?:string}[]}> {
  const r = await fetch('/admin/category/tree/move', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({...payload, dryRun: false})
  });
  return await r.json();
}
