import {Button, TextField} from "@mui/material";
import React, {useState} from "react";
import {observer} from "mobx-react-lite";
import {useStore} from "../../../main/context-provider";
import {Form} from "react-router-dom";


export const RegisterPage = observer(() => {
    const {authStore} = useStore();

    const [login, setLogin] = useState("");
    const [password, setPassword] = useState("");

    const [formLoading, setFormLoading] = useState(false);

    function handleSubmit() {
        setFormLoading(true);
        authStore.submitRegisterForm({login, password}).then(() => setFormLoading(false));
    }

    return (
        <Form onSubmit={handleSubmit}>
            <div className="flex justify-center items-center">
                <div className="flex items-center justify-center w-72 flex-col gap-2">
                    <div className="font-light text-2xl text-center">Регистрация</div>
                    <TextField
                        label="Логин"
                        variant="outlined"
                        value={login}
                        onChange={(event) => setLogin(event.target.value)}
                    />
                    <TextField
                        label="Пароль"
                        variant="outlined"
                        value={password}
                        onChange={(event) => setPassword(event.target.value)}
                    />
                    <div className="flex justify-center w-2/3">
                        <Button variant="outlined" type={"submit"} disabled={formLoading}>
                            Зарегистрироваться
                        </Button>
                    </div>
                </div>
            </div>
        </Form>
    )
})