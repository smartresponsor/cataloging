export class CategoryClient {
  constructor(private baseUrl: string) {}
  async list(): Promise<any[]> {
    const r = await fetch(`${this.baseUrl}/api/category/storefront`);
    return await r.json();
  }
}
