import {theme} from "../theme";
import Typography from "@mui/material/Typography";
import {Link} from "react-router-dom";

export const NotFoundPage = () => {
    return (
        <div className="flex justify-center">
            <span className="flex gap-4">
                <img src="/public/images/optimized/404.webp" alt="404 picture"/>
                <div className="flex flex-col gap-4">
                    <Typography color={theme.colorPrimary}>
                        <span className={"font-bold text-3xl"}>Ошибка 404. Страница не найдена</span>
                    </Typography>
                    <span className={"font-extralight text-3xl"}>А вот и нет тут ничего</span>
                    <Typography color={theme.colorPrimary}>
                        <Link to={"/"} className="text-2xl font-extrabold underline">На главную</Link>
                    </Typography>
                </div>
            </span>
        </div>
)
}

