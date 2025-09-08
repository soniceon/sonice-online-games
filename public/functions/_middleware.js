export function onRequest(context) {
  // 如果是根路径，重定向到 index.html
  if (context.request.url.endsWith('/') || context.request.url.endsWith('/index.html')) {
    return context.next();
  }
  
  // 对于其他路径，也返回 index.html
  return context.next();
}
