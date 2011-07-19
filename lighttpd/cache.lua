--loading md5 lib
require "md5"

-- print ("loading")

-- ----------------------------------------------------------------------------
-- CONFIGURATION
enableUserAgentCheck = false
-- Set default for sendCached file
sendCachedFile = true
expiration_time = 3*60
-- ----------------------------------------------------------------------------

function serve_html(cached_page, expiration_time)
  attr = lighty.stat(cached_page)
  --print ("serving plain file")
  --Check if the cached file has expired
  if (attr and (attr['st_mtime'] + expiration_time) > os.time() ) then
    lighty.header["Content-Type"] = "text/html"
    lighty.env["physical.path"] = cached_page
    return true
  else
    return false
  end
end

function serve_gzip(cached_page, expiration_time)
  attr = lighty.stat(cached_page .. ".gz")
  --print ("serving gzip file")
  --Check if the gziped cached file has expired
  if (attr and  (attr['st_mtime'] + expiration_time) > os.time() ) then
    lighty.header["Content-Encoding"] = "gzip"
    lighty.header["Content-Type"] = "text/html"
    lighty.env["physical.path"] = cached_page .. ".gz"
    return true
  else
    return false
  end
end

--lighty.header["Content-Type"] = "text/html"
--content are compressed by PHP
--you should enable this only for browsers that support gzip encoding.but which doesn.t!
--lighty.header["Content-Encoding"] = "gzip"





-- print ("checking path");
attr = lighty.stat(lighty.env["physical.path"])

-- if (attr) then
-- print("attr true")
-- else
-- print("attr false")
-- end


if (not attr) then
  lighty.env["uri.path"] = "/index.php"
  lighty.env["physical.rel-path"] = lighty.env["uri.path"]
  lighty.env["physical.path"] = lighty.env["physical.doc-root"] .. lighty.env["physical.rel-path"]
end
  -- Sending a HTTP query? => no caching
  query_condition = not (lighty.env["uri.query"] and string.find(lighty.env["uri.query"], ".*s=.*"))


  -- checking if request is a POST
  post_condition = not (string.find(lighty.env["request.method"], "POST"))

  -- Have a cookie? => no caching
  user_cookie = lighty.request["Cookie"] or ""
  cookie_condition = not (string.find(user_cookie,"ak"))
-- string.find(user_cookie, ".*comment_author.*") or string.find(user_cookie, ".*wordpress.*") or string.find(user_cookie, ".*wp-postpass_.*") or string.find(user_cookie,".*ak.*"))

  if (not enableUserAgentCheck) then
    sendCachedFile = true
    -- DEBUG print("because of the enableUserAgentCheck sendCachedFile was just set to true")
  else
    -- Check if request comes from a mobile device or bot => no caching than, either.
    local userAgentsCacheWhitelist = { "ipad" }
    local userAgentsNoCaching = { "iphone", "ipod", "android", "cupcake", "webos", "incognito", "webmate", "opera mini", "blackberry", "symbian", "series60", "nokia", "samsung", "playstation", "iemobile" , "midp-2.0", "lg/u990", "eudoraweb", "googlebot-mobile", "240x320", "opera mobi", "mmp", "avantgo", "blazer", "winwap", "kyocera/wx310k", "msnbot-mobile", "smartphone", "mobilerss", "mobile", "sonyericsson", "playstation portable", "kindle", "midp", "ts21i-10", "xda", "treo", "vodafone", "netfront", "newt", "docomo"}
-- Android, 2.0 MMP, 240x320, AvantGo, BlackBerry, Blazer, Cellphone, Danger, DoCoMo, Elaine/3.0, EudoraWeb, hiptop, IEMobile, iPhone, iPod, KYOCERA/WX310K, LG/U990, MIDP-2.0, MMEF20, MOT-V, NetFront, Newt, Nintendo Wii, Nitro, Nokia, Opera Mini, Palm, Playstation Portable, portalmmm, Proxinet, ProxiNet, SHARP-TQ-GX10, Small, SonyEricsson, Symbian OS, SymbianOS, TS21i-10, UP.Browser, UP.Link, Windows CE, WinWAP, Mobile, MobileRSS, Googlebot-Mobile, MSNBOT-MOBILE, Smartphone, Configuration/CLDC/, hp , hp- , htc, htc_, htc-, kindle, MIDP, motorola, opera mobi, PalmOS, pocket, ppc; , sqh, spv, symbian, treo,  vodafone, xda, xda_'
    userAgent = lighty.request["User-Agent"]
    -- DEBUG print(userAgent)
    if (nil == userAgent) then
      sendCachedFile = true
    else
      userAgent = string.lower(userAgent)
      -- DEBUG print("string lower of userAgent was just run")
      -- DEBUG print(userAgent)
      for i, v in ipairs(userAgentsNoCaching) do
        if string.find(userAgent, v) then
          sendCachedFile = false
              break
        end
      end
      -- now lets reverse it if the agent is on the whitelist!!
      for i, v in ipairs(userAgentsCacheWhitelist) do
        if string.find(userAgent, v) then
          sendCachedFile = true
              break
        end
      end
    end
  end


--DEBUG LINES
--  if (query_condition) then
--  print("query true")
--  else
--  print("query false")
--  end
--
--  if (cookie_condition) then
--  print("cookie true")
--  else
--  print("cookie false")
--  end
--
--  if (sendCachedFile) then
--  print("sendCachedFile true")
--  else
--  print("sendCachedFile false")
--  end
-- END DEBUG LINES


-- print("checking conditions")

  if (query_condition and cookie_condition and post_condition and sendCachedFile) then
-- print("sending cached file")
--    print(user_cookie);
    accept_encoding = lighty.request["Accept-Encoding"] or "no_acceptance"
    cached_page = "/dev/shm/lua/" .. md5.sumhexa(lighty.env["request.uri"]) .. user_cookie
    cached_page = string.gsub(cached_page, "index.php/", "/")
    cached_page = string.gsub(cached_page, "//", "/")
    if (string.find(accept_encoding, "gzip")) then
      if not serve_gzip(cached_page, expiration_time) then serve_html(cached_page, expiration_time) end
    else
      serve_html(cached_page, expiration_time)
    end
  end
-- print("at the end");
-- end
-- print("at the VERY end");



--local file = "/dev/shm/lua/" .. md5.sumhexa(lighty.env["request.uri"])
--if lighty.stat(file) then
--lighty.content = { { filename = file } }
--lighty.header["Content-Type"] = "text/html"
--content are compressed by PHP
--you should enable this only for browsers that support gzip encoding.but which doesn.t!
--lighty.header["Content-Encoding"] = "gzip"
--return 200
--end

