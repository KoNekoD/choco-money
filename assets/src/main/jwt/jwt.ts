const getTokenExpirationDate = (token: string) => {
  const result = new Date(0); // The 0 here is the key, which sets the date to the epoch
  if (!token) return result;
  try {
    const decoded = parseJwt<{ exp: string }>(token);
    if (!decoded.exp) {
      return result;
    }
    // @ts-ignore
    result.setUTCSeconds(decoded.exp);
    return result;
  } catch (e) {
    return new Date('01-01-1970');
  }
};

export const isTokenExpired = (token: string): boolean => {
  const date = getTokenExpirationDate(token);
  const offsetSeconds = 0;
  if (date.getTime() - new Date().getTime() < 0) {
    return true;
  }
  return !(date.valueOf() > new Date().valueOf() + offsetSeconds * 1000);
};

export const parseJwt = <T>(token: string): T => {
  const base64Url = token.split('.')[1];
  if (!base64Url) {
    throw new Error(`RBook error: invalid JWT - ${token}`);
  }
  const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');

  const jsonPayload = decodeURIComponent(
    atob(base64)
      .split('')
      .map(function (c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
      })
      .join('')
  );

  return JSON.parse(jsonPayload);
};
