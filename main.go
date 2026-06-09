package main

import (
    "log"
    "net/http"
    "net/http/httputil"
    "net/url"
)

func main() {
    targetURL := "https://agentrouter.org"
    target, _ := url.Parse(targetURL)
    proxy := httputil.NewSingleHostReverseProxy(target)

    originalDirector := proxy.Director
    proxy.Director = func(req *http.Request) {
        originalDirector(req)
        req.Host = target.Host
        req.Header.Set("Originator", "codex_cli_rs")
        req.Header.Set("User-Agent", "codex_cli_rs/0.101.0 (Mac OS 26.0.1; arm64) Apple_Terminal/464")
        req.Header.Set("Version", "0.101.0")
    }

    log.Println("✅ Proxy running on http://localhost:8318")
    http.ListenAndServe(":8318", proxy)
}