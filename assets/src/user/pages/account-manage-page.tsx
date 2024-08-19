import {observer} from "mobx-react-lite";
import {useStore} from "../../main/context-provider";
import {
  AppBar,
  Button,
  Card,
  CardActionArea,
  CardActions,
  CardContent,
  Chip,
  Grid,
  IconButton,
  Paper,
  Skeleton,
  TextField,
  Toolbar,
  Tooltip
} from "@mui/material";
import React, {useEffect} from "react";
import {Refresh, Search, SupervisedUserCircle} from "@mui/icons-material";
import {useNavigate} from "react-router-dom";

export const AccountManagePage = observer(() => {
    const {apiClient, userStore} = useStore();
    const navigate = useNavigate();

    const page = 1;

    useEffect(() => {
        if (page) {
            userStore.fetchProfilesList(page);
            userStore.fetchUserInfo();
        }
    }, [page]);

    return (
        <Paper sx={{maxWidth: 936, margin: 'auto', overflow: 'hidden'}}>
            <AppBar
                position="static"
                color="default"
                elevation={0}
                sx={{borderBottom: '1px solid rgba(0, 0, 0, 0.12)'}}
            >
                <Toolbar>
                    <Grid container spacing={2} alignItems="center">
                        <Grid item>
                            <SupervisedUserCircle color="inherit" sx={{display: 'block'}}/>
                        </Grid>
                        <Grid item xs>
                            <div>
                                {userStore.userInfo?.state !== 'fulfilled' &&
                                    <div className="flex gap-2">
                                        {Array(3).fill(null)
                                            .map((_, i) => (
                                                <Skeleton key={i} className="w-full"/>
                                            ))}
                                    </div>
                                }
                                { userStore.userInfo?.state === 'fulfilled' &&
                                    <div className="flex gap-2">
                                        <Chip label="Ваш аккаунт:"/>
                                        <Chip label={`ID:${userStore.userInfo.value?.id}`}/>
                                        <Chip label={`Username:${userStore.userInfo.value?.login}`}/>
                                    </div>
                                }
                            </div>
                        </Grid>
                        <Grid item>
                            <Tooltip title="Reload">
                                <IconButton onClick={() => {
                                    userStore.fetchUserInfo()
                                }}>
                                    <Refresh color="inherit" sx={{display: 'block'}}/>
                                </IconButton>
                            </Tooltip>
                        </Grid>
                    </Grid>
                </Toolbar>
                <Toolbar>
                    <Grid container spacing={2} alignItems="center">
                        <Grid item>
                            <Search color="inherit" sx={{display: 'block'}}/>
                        </Grid>
                        <Grid item xs>
                            <TextField
                                fullWidth
                                placeholder="Поиск по имени или фамилии(или ID)"
                                InputProps={{
                                    disableUnderline: true,
                                    sx: {fontSize: 'default'},
                                }}
                                variant="standard"
                            />
                        </Grid>
                        <Grid item>
                            <Button variant="contained" sx={{mr: 1}}
                                    onClick={() => navigate("/profile/+")}
                            >
                                Создать профиль
                            </Button>
                            <Tooltip title="Reload">
                                <IconButton onClick={() => {
                                    userStore.fetchProfilesList(page)
                                }}>
                                    <Refresh color="inherit" sx={{display: 'block'}}/>
                                </IconButton>
                            </Tooltip>
                        </Grid>
                    </Grid>
                </Toolbar>
            </AppBar>
            <div>
                {userStore.profilesList?.state === 'pending' &&
                    <div className="grid grid-cols-3 gap-2 p-2">
                        {Array(10)
                            .fill(null)
                            .map((_, i) => (

                                <Card key={i}>
                                    <CardActionArea>
                                        <Skeleton height="140"/>
                                        <CardContent>
                                            <Skeleton/>
                                            <Skeleton/>
                                        </CardContent>
                                    </CardActionArea>
                                    <CardActions>
                                        <Button size="small" color="primary">
                                            <Skeleton className="w-full"/>
                                        </Button>
                                    </CardActions>
                                </Card>
                            ))}
                    </div>
                }
            </div>
        </Paper>
    );
});
